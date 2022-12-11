<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\DI;

use ReflectionClass;
use RuntimeException;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use ReflectionException;
use Nette\Loaders\RobotLoader;
use Nette\DI\CompilerExtension;
use Nette\Application\IPresenter;
use Nette\Application\UI\Control;
use Composer\Autoload\ClassLoader;
use Nette\Application\UI\Presenter;
use Nette\DI\Definitions\Reference;
use Nette\DI\Definitions\Statement;
use Nette\DI\Definitions\Definition;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\Definitions\ServiceDefinition;
use SixtyEightPublishers\SmartNetteComponent\Reader\AttributesMap;
use SixtyEightPublishers\SmartNetteComponent\Attribute\AttributePrototype;
use SixtyEightPublishers\SmartNetteComponent\Reader\StaticAttributeReader;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleHandlerInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizatorAwareInterface;
use function assert;
use function dirname;
use function is_file;
use function sprintf;
use function array_map;
use function array_keys;
use function array_merge;
use function array_filter;
use function array_unique;
use function array_values;
use function sys_get_temp_dir;

final class ComponentAuthorizationExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		$parameters = $this->getContainerBuilder()->parameters;
		$debugMode = (bool) ($parameters['debugMode'] ?? false);
		$scanDirs = array_filter([$parameters['appDir'] ?? null]);

		return Expect::structure([
			'cache' => Expect::bool($debugMode),
			'scanDirs' => Expect::anyOf(
				Expect::arrayOf('string')->default($scanDirs)->mergeDefaults(),
				false
			)->firstIsDefault(),
			'scanComposer' => Expect::bool(class_exists(ClassLoader::class)),
			'scanFilters' => Expect::arrayOf('string')
				->default([
					'*Presenter',
					'*Control',
					'*Component',
				])
				->mergeDefaults(),
		])->castTo(ComponentAuthorizationConfig::class);
	}

	public function loadConfiguration(): void
	{
		$this->loadDefinitionsFromConfig($this->loadFromFile(__DIR__ . '/services.neon')['services']);
	}

	/**
	 * @throws ReflectionException
	 */
	public function beforeCompile(): void
	{
		$config = $this->getConfig();
		assert($config instanceof ComponentAuthorizationConfig);

		if ($config->cache) {
			$this->registerStaticAttributeReader($config);
		}

		$this->registerRuleHandlers();
		$this->awareComponentAuthorizatorService();
	}

	/**
	 * @throws ReflectionException
	 */
	private function registerStaticAttributeReader(ComponentAuthorizationConfig $config): void
	{
		$builder = $this->getContainerBuilder();
		$classes = [];

		if (!$config->scanDirs && !$config->scanComposer) {
			throw new RuntimeException(sprintf(
				'Can\'t create a cached attribute reader because both options %s and %s are disabled.',
				$this->prefix('scanDirs'),
				$this->prefix('scanComposer')
			));
		}

		if ($config->scanDirs) {
			if (!\class_exists(RobotLoader::class)) {
				throw new RuntimeException(sprintf(
					'RobotLoader is required to create cached attribute reader, install package "nette/robot-loader" or disable option %s: false.',
					$this->prefix('scanDirs')
				));
			}

			$tempDir = ($builder->parameters['tempDir'] ?? sys_get_temp_dir()) . '/cache/68publishers.smart-nette-components';
			$loader = new RobotLoader();
			$loader->acceptFiles = array_map(static fn (string $filter): string => $filter . '.php', $config->scanFilters);

			$loader->addDirectory(...$config->scanDirs);
			$loader->setTempDirectory($tempDir);
			$loader->refresh();

			$classes = array_keys($loader->getIndexedClasses());
		}

		if ($config->scanComposer) {
			$classLoaderReflection = new ReflectionClass(ClassLoader::class);
			$classFile = dirname($classLoaderReflection->getFileName()) . '/autoload_classmap.php';

			if (is_file($classFile)) {
				$builder->addDependency($classFile);
				$classes = array_merge($classes, array_keys((static fn (string $path) => require $path)($classFile)));
			}
		}

		$classList = [];

		foreach (array_unique($classes) as $class) {
			$matched = false;

			foreach ($config->scanFilters as $scanFilter) {
				if (fnmatch($scanFilter, $class)) {
					$matched = true;

					break;
				}
			}

			if (!$matched || !\class_exists($class)) {
				continue;
			}

			$classReflection = new ReflectionClass($class);

			if (Control::class === $classReflection->getName()
				|| Presenter::class === $classReflection->getName()
				|| !($classReflection->isSubclassOf(Control::class) || $classReflection->implementsInterface(IPresenter::class))
			) {
				continue;
			}

			$classList[] = $classReflection->getName();
		}

		$map = AttributesMap::createFromClassList($classList, true);
		$reader = $builder->getDefinition($this->prefix('reader.inner'));
		assert($reader instanceof ServiceDefinition);

		$reader->setType(StaticAttributeReader::class);
		$reader->setFactory(StaticAttributeReader::class, [
			'map' => new Statement(AttributesMap::class, [
				'classHierarchy' => $map->classHierarchy,
				'attributes' => array_map(
					static fn (array $attrs): array => array_map(
						static fn (AttributePrototype $prototype): Statement => new Statement($prototype->classname, $prototype->arguments),
						$attrs
					),
					$map->attributes
				),
			]),
		]);
	}

	private function registerRuleHandlers(): void
	{
		$builder = $this->getContainerBuilder();
		$handlerRegistry = $builder->getDefinition($this->prefix('authorization.handler'));
		assert($handlerRegistry instanceof ServiceDefinition);

		$handlers = array_filter(
			$builder->findByType(RuleHandlerInterface::class),
			static fn (Definition $definition): bool => $definition !== $handlerRegistry
		);

		$handlerRegistry->setArgument('handlers', array_values($handlers));
	}

	private function awareComponentAuthorizatorService(): void
	{
		$definitions = array_filter(
			$this->getContainerBuilder()->getDefinitions(),
			static fn (Definition $def): bool =>
				is_a($def->getType(), ComponentAuthorizatorAwareInterface::class, true)
				|| ($def instanceof FactoryDefinition && is_a($def->getResultType(), ComponentAuthorizatorAwareInterface::class, true))
		);

		foreach ($definitions as $definition) {
			if ($definition instanceof FactoryDefinition) {
				$definition = $definition->getResultDefinition();
			}

			$definition->addSetup('setComponentAuthorizator', [
				new Reference($this->prefix('authorization.component_authorizator')),
			]);
		}
	}
}

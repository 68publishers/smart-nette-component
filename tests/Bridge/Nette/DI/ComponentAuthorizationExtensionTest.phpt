<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Bridge\Nette\DI;

use Closure;
use Tester\Assert;
use Tester\TestCase;
use RuntimeException;
use Nette\DI\Container;
use NetteModule\ErrorPresenter;
use NetteModule\MicroPresenter;
use Tester\CodeCoverage\Collector;
use SixtyEightPublishers\SmartNetteComponent\Attribute\InRole;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedIn;
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedOut;
use SixtyEightPublishers\SmartNetteComponent\Reader\AttributeReader;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\AComponent;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\APresenter;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\BComponent;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\BPresenter;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\CPresenter;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\DPresenter;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\EPresenter;
use SixtyEightPublishers\SmartNetteComponent\Reader\StaticAttributeReader;
use SixtyEightPublishers\SmartNetteComponent\Tests\Reader\CustomRuleHandler;
use SixtyEightPublishers\SmartNetteComponent\Reader\AttributeReaderInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleHandlerInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizator;
use SixtyEightPublishers\SmartNetteComponent\Authorization\Handler\InRoleHandler;
use SixtyEightPublishers\SmartNetteComponent\Reader\RuntimeCachedAttributeReader;
use SixtyEightPublishers\SmartNetteComponent\Authorization\Handler\AllowedHandler;
use SixtyEightPublishers\SmartNetteComponent\Authorization\Handler\HandlerRegistry;
use SixtyEightPublishers\SmartNetteComponent\Authorization\Handler\LoggedInHandler;
use SixtyEightPublishers\SmartNetteComponent\Authorization\Handler\LoggedOutHandler;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\AComponentFactoryInterface;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\BComponentFactoryInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizatorInterface;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\ServiceWithComponentAuthorizator;
use function assert;
use function call_user_func;

require __DIR__ . '/../../../bootstrap.php';

final class ComponentAuthorizationExtensionTest extends TestCase
{
	public function testExceptionShouldBeThrownIfCacheEnabledButScanDirsAndScanComposerDisabled(): void
	{
		Assert::exception(
			static fn () => ContainerFactory::create(__DIR__ . '/config.error.cacheEnabledButScanDirsAndScanComposerDisabled.neon'),
			RuntimeException::class,
			'Can\'t create a cached attribute reader because both options smart_nette_components.scanDirs and smart_nette_components.scanComposer are disabled.'
		);
	}

	public function testExtensionIntegrationWithoutCache(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config.withoutCache.neon');
		$authorizator = $this->assertComponentAuthorizatorShouldBeRegistered($container);
		$innerReader = $this->getInnerReader($container);

		$this->assertHandlersShouldBeRegistered($authorizator);
		$this->assertReaderShouldBeAttributeReader($innerReader);
	}

	public function testExtensionIntegrationWithCache(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config.withCache.neon');
		$authorizator = $this->assertComponentAuthorizatorShouldBeRegistered($container);
		$innerReader = $this->getInnerReader($container);

		$this->assertHandlersShouldBeRegistered($authorizator);
		$this->assertReaderShouldBeStaticAttributeReader(
			$innerReader,
			[
				AComponent::class => false,
				BComponent::class => AComponent::class,
				APresenter::class => false,
				BPresenter::class => false,
				CPresenter::class => APresenter::class,
				DPresenter::class => APresenter::class,
				EPresenter::class => CPresenter::class,
				ErrorPresenter::class => false,
				MicroPresenter::class => false,
			],
			[
				APresenter::class . '::handleSignalA' => [
					new Allowed('resourceA', 'signalA'),
				],
				BPresenter::class . '::class' => [
					new LoggedOut(),
					new InRole('admin'),
				],
				CPresenter::class . '::class' => [
					new LoggedIn(),
				],
				CPresenter::class . '::actionDefault' => [
					new Allowed('resourceC', 'actionC'),
				],
				DPresenter::class . '::actionDefault' => [
					new Allowed('resourceD', 'actionD'),
				],
				DPresenter::class . '::handleSignalD' => [
					new Allowed('resourceD', 'signalD'),
					new LoggedIn(),
				],
				EPresenter::class . '::class' => [
					new InRole('customer'),
				],
				EPresenter::class . '::actionDefault' => [
					new Allowed('resourceE', 'actionE'),
				],
				EPresenter::class . '::handleSignalA' => [
					new Allowed('resourceE', 'signalE'),
				],
				EPresenter::class . '::handleSignalE' => [
					new Allowed('resourceE', 'signalE'),
				],
				AComponent::class . '::handleSignalA' => [
					new InRole('admin'),
				],
				BComponent::class . '::handleSignalA' => [
					new Allowed('resourceB', 'signalA'),
				],
				BComponent::class . '::handleSignalB' => [
					new LoggedIn(),
				],
			]
		);
	}

	public function testExtensionIntegrationWithCustomRuleHandler(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config.withCustomRuleHandler.neon');
		$authorizator = $this->assertComponentAuthorizatorShouldBeRegistered($container);

		$this->assertHandlersShouldBeRegistered($authorizator, [
			CustomRuleHandler::class,
		]);
	}

	public function testComponentAuthorizatorShouldBeAware(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config.awareComponentAuthorizator.neon');
		$aComponentFactory = $container->getByType(AComponentFactoryInterface::class);
		$bComponentFactory = $container->getByType(BComponentFactoryInterface::class);
		assert($aComponentFactory instanceof AComponentFactoryInterface && $bComponentFactory instanceof BComponentFactoryInterface);

		$services = [
			$container->getByName('bPresenter'),
			$container->getByName('dPresenter'),
			$container->getByName('ePresenter'),
			$aComponentFactory->create(),
			$bComponentFactory->create(),
			$container->getByType(ServiceWithComponentAuthorizator::class),
		];

		foreach ($services as $service) {
			call_user_func(Closure::bind(
				static function () use ($service) {
					Assert::type(ComponentAuthorizatorInterface::class, $service->componentAuthorizator);
				},
				null,
				\get_class($service)
			));
		}
	}

	public function assertComponentAuthorizatorShouldBeRegistered(Container $container): ComponentAuthorizator
	{
		$authorizator = $container->getByType(ComponentAuthorizatorInterface::class);

		Assert::type(ComponentAuthorizator::class, $authorizator);
		assert($authorizator instanceof ComponentAuthorizator);

		return $authorizator;
	}

	protected function tearDown(): void
	{
		# save manually partial code coverage to free memory
		if (Collector::isStarted()) {
			Collector::save();
		}
	}

	private function assertHandlersShouldBeRegistered(ComponentAuthorizator $authorizator, array $customHandlers = []): void
	{
		$expected = array_merge([
			AllowedHandler::class,
			InRoleHandler::class,
			LoggedInHandler::class,
			LoggedOutHandler::class,
		], $customHandlers);

		call_user_func(Closure::bind(
			static function () use ($authorizator, $expected): void {
				$handler = $authorizator->handler;

				Assert::type(HandlerRegistry::class, $handler);
				assert($handler instanceof HandlerRegistry);

				call_user_func(Closure::bind(
					static function () use ($handler, $expected): void {
						$handlers = array_map(static fn (RuleHandlerInterface $h): string => get_class($h), $handler->handlers);

						Assert::equal($expected, $handlers);
					},
					null,
					HandlerRegistry::class
				));
			},
			null,
			ComponentAuthorizator::class
		));
	}

	private function getInnerReader(Container $container): AttributeReaderInterface
	{
		$reader = $container->getByType(AttributeReaderInterface::class);

		Assert::type(RuntimeCachedAttributeReader::class, $reader);
		assert($reader instanceof RuntimeCachedAttributeReader);

		$innerReader = null;

		call_user_func(Closure::bind(
			static function () use ($reader, &$innerReader): void {
				$innerReader = $reader->inner;
			},
			null,
			RuntimeCachedAttributeReader::class
		));

		assert($innerReader instanceof AttributeReaderInterface);

		return $innerReader;
	}

	private function assertReaderShouldBeAttributeReader(AttributeReaderInterface $reader): void
	{
		Assert::type(AttributeReader::class, $reader);
	}

	private function assertReaderShouldBeStaticAttributeReader(AttributeReaderInterface $reader, array $expectedClassHierarchy, array $expectedAttributes): void
	{
		Assert::type(StaticAttributeReader::class, $reader);
		assert($reader instanceof StaticAttributeReader);

		call_user_func(Closure::bind(
			static function () use ($reader, $expectedClassHierarchy, $expectedAttributes): void {
				$map = $reader->map;

				Assert::equal($expectedClassHierarchy, $map->classHierarchy);
				Assert::equal($expectedAttributes, $map->attributes);
			},
			null,
			StaticAttributeReader::class
		));
	}
}

(new ComponentAuthorizationExtensionTest())->run();

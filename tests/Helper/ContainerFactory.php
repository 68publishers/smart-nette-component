<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Helper;

use Nette;
use Tester;
use SixtyEightPublishers;

final class ContainerFactory
{
	/**
	 * @param string            $name
	 * @param string|array|NULL $config
	 *
	 * @return \Nette\DI\Container
	 */
	public static function createContainer(string $name, $config = NULL): Nette\DI\Container
	{
		if (!defined('TEMP_PATH')) {
			define('TEMP_PATH', __DIR__ . '/../temp');
		}

		$loader = new Nette\DI\ContainerLoader(TEMP_PATH . '/cache/Nette.Configurator/', TRUE);
		$class = $loader->load(static function (Nette\DI\Compiler $compiler) use ($config): void {
			$compiler->addExtension('http', new Nette\Bridges\HttpDI\HttpExtension());
			$compiler->addExtension('session', new Nette\Bridges\HttpDI\SessionExtension());
			$compiler->addExtension('security', new Nette\Bridges\SecurityDI\SecurityExtension());
			$compiler->addExtension('smart_nette_component', new SixtyEightPublishers\SmartNetteComponent\DI\SmartNetteComponentExtension());

			if (is_array($config)) {
				$compiler->addConfig($config);
			} elseif (is_string($config) && is_file($config)) {
				$compiler->loadConfig($config);
			} elseif (NULL !== $config) {
				$compiler->loadConfig(Tester\FileMock::create((string) $config, 'neon'));
			}
		}, $name);

		return new $class();
	}
}

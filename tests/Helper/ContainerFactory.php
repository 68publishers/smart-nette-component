<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Helper;

use Nette\Configurator;
use Nette\DI\Container;

final class ContainerFactory
{
	/**
	 * @param string|array|NULL $config
	 *
	 * @return \Nette\DI\Container
	 */
	public static function createContainer($config = NULL): Container
	{
		$configurator = new Configurator();

		$configurator->setTempDirectory(TEMP_PATH);
		$configurator->addConfig(CONFIG_DIR . '/common.neon');

		if (NULL !== $config) {
			$configurator->addConfig($config);
		}

		return $configurator->createContainer();
	}
}

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Helper;

use Nette;

final class ContainerFactory
{
	/**
	 * @param string|array|NULL $config
	 *
	 * @return \Nette\DI\Container
	 */
	public static function createContainer($config = NULL): Nette\DI\Container
	{
		$configurator = new Nette\Configurator();

		$configurator->setTempDirectory(TEMP_PATH);
		$configurator->addConfig(CONFIG_DIR . '/common.neon');

		if (NULL !== $config) {
			$configurator->addConfig($config);
		}

		return $configurator->createContainer();
	}
}

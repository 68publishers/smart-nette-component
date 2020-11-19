<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Cases\DI;

use Nette;
use Tester;
use SixtyEightPublishers;

require __DIR__ . '/../../bootstrap.php';

final class SmartNetteComponentExtensionIntegrationTest extends Tester\TestCase
{
	/**
	 * @return void
	 */
	public function testExceptionOnMissingDoctrineAnnotationReader(): void
	{
		Tester\Assert::exception(
			static function () {
				SixtyEightPublishers\SmartNetteComponent\Tests\Helper\ContainerFactory::createContainer(CONFIG_DIR . '/config_without_reader.neon');
			},
			SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException::class,
			'Missing service of type Doctrine\Common\Annotations\Reader. Please register it manually or use one of suggested libraries from composer.json'
		);
	}

	/**
	 * @return void
	 */
	public function testRegisteredDependencies(): void
	{
		/** @var NULL|\Nette\DI\Container $container */
		$container = NULL;

		Tester\Assert::noError(static function () use (&$container) {
			$container = SixtyEightPublishers\SmartNetteComponent\Tests\Helper\ContainerFactory::createContainer(CONFIG_DIR . '/config.neon');
		});

		Tester\Assert::type(SixtyEightPublishers\SmartNetteComponent\Reader\IAnnotationReader::class, $container->getService('smart_nette_component.reader'));
		Tester\Assert::type(SixtyEightPublishers\SmartNetteComponent\Link\ILinkAuthorizator::class, $container->getService('smart_nette_component.link_authorizator'));
	}
}

(new SmartNetteComponentExtensionIntegrationTest())->run();

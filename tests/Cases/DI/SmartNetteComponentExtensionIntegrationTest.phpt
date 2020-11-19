<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Cases\DI;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\SmartNetteComponent\Tests\Helper\ContainerFactory;
use SixtyEightPublishers\SmartNetteComponent\Link\LinkAuthorizatorInterface;
use SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException;
use SixtyEightPublishers\SmartNetteComponent\Reader\AnnotationReaderInterface;

require __DIR__ . '/../../bootstrap.php';

final class SmartNetteComponentExtensionIntegrationTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testExceptionOnMissingDoctrineAnnotationReader(): void
	{
		Assert::exception(
			static function () {
				ContainerFactory::createContainer(CONFIG_DIR . '/config_without_reader.neon');
			},
			InvalidStateException::class,
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

		Assert::noError(static function () use (&$container) {
			$container = ContainerFactory::createContainer(CONFIG_DIR . '/config.neon');
		});

		Assert::type(AnnotationReaderInterface::class, $container->getService('smart_nette_component.reader'));
		Assert::type(LinkAuthorizatorInterface::class, $container->getService('smart_nette_component.link_authorizator'));
	}
}

(new SmartNetteComponentExtensionIntegrationTest())->run();

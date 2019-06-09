<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Cases;

use Tester;
use SixtyEightPublishers;

require __DIR__ . '/../../bootstrap.php';

final class SmartNetteComponentExtensionIntegrationTest extends Tester\TestCase
{
	/**
	 * @return void
	 */
	public function testRegisteredDependencies(): void
	{
		$container = SixtyEightPublishers\SmartNetteComponent\Tests\Helper\ContainerFactory::createContainer(
			static::class  . __METHOD__,
			__DIR__ . '/../../files/doctrine_annotation_reader.neon'
		);

		Tester\Assert::type(SixtyEightPublishers\SmartNetteComponent\Reader\IAnnotationReader::class, $container->getService('smart_nette_component.reader'));
	}

	/**
	 * @return void
	 */
	public function testMissingDoctrineAnnotationReader(): void
	{
		Tester\Assert::exception(
			function () {
				SixtyEightPublishers\SmartNetteComponent\Tests\Helper\ContainerFactory::createContainer(
					static::class  . __METHOD__
				);
			},
			SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException::class,
			'Missing service of type Doctrine\Common\Annotations\Reader. Please register it manually or use one of suggested libraries from composer.json'
		);
	}
}

(new SmartNetteComponentExtensionIntegrationTest())->run();

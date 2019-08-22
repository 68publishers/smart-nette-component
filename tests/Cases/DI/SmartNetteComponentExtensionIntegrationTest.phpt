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
				SixtyEightPublishers\SmartNetteComponent\Tests\Helper\ContainerFactory::createContainer(
					static::class  . '::testExceptionOnMissingDoctrineAnnotationReader'
				);
			},
			SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException::class,
			'Missing service of type Doctrine\Common\Annotations\Reader. Please register it manually or use one of suggested libraries from composer.json'
		);
	}

	/**
	 * @return void
	 */
	public function testExceptionOnMissingTranslator(): void
	{
		Tester\Assert::exception(
			static function () {
				SixtyEightPublishers\SmartNetteComponent\Tests\Helper\ContainerFactory::createContainer(
					static::class  . '::testExceptionOnMissingTranslator',
					__DIR__ . '/../../files/missing_translator.neon'
				);
			},
			SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException::class,
			'There is 1 service that implements interface SixtyEightPublishers\SmartNetteComponent\Translator\ITranslatorAware but service of type Nette\Localization\ITranslator is not registered.'
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
			$container = SixtyEightPublishers\SmartNetteComponent\Tests\Helper\ContainerFactory::createContainer(
				static::class  . '::testRegisteredDependencies',
				__DIR__ . '/../../files/valid_config.neon'
			);
		});

		Tester\Assert::type(SixtyEightPublishers\SmartNetteComponent\Reader\IAnnotationReader::class, $container->getService('smart_nette_component.reader'));
		Tester\Assert::type(Nette\Localization\ITranslator::class, $container->getService('dummyService')->getTranslator());
	}
}

(new SmartNetteComponentExtensionIntegrationTest())->run();

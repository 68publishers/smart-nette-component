<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Cases;

use Tester;
use Mockery;
use SixtyEightPublishers;

require __DIR__ . '/../../bootstrap.php';

final class ClassAnnotationTest extends Tester\TestCase
{
	/**
	 * {@inheritdoc}
	 */
	protected function tearDown(): void
	{
		parent::tearDown();

		Mockery::close();
	}

	/**
	 * @return void
	 */
	public function testBase(): void
	{
		$classAnnotation = new SixtyEightPublishers\SmartNetteComponent\Reader\ClassAnnotation(
			$reflection = Mockery::mock(\ReflectionClass::class),
			$object = new \stdClass()
		);

		Tester\Assert::same($reflection, $classAnnotation->getReflectionClass());
		Tester\Assert::same($reflection, $classAnnotation->reflectionClass);

		Tester\Assert::same($object, $classAnnotation->getAnnotation());
		Tester\Assert::same($object, $classAnnotation->annotation);
	}
}

(new ClassAnnotationTest())->run();

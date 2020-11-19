<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Cases\Reader;

use Mockery;
use stdClass;
use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\SmartNetteComponent\Reader\ClassAnnotation;

require __DIR__ . '/../../bootstrap.php';

final class ClassAnnotationTest extends TestCase
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
		$classAnnotation = new ClassAnnotation(
			$reflection = Mockery::mock(\ReflectionClass::class),
			$object = new stdClass()
		);

		Assert::same($reflection, $classAnnotation->getReflectionClass());
		Assert::same($reflection, $classAnnotation->reflectionClass);

		Assert::same($object, $classAnnotation->getAnnotation());
		Assert::same($object, $classAnnotation->annotation);
	}
}

(new ClassAnnotationTest())->run();

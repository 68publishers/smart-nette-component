<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Cases;

use Tester;
use Mockery;
use Doctrine;
use SixtyEightPublishers;

require __DIR__ . '/../../bootstrap.php';

final class DoctrineAnnotationReaderTest extends Tester\TestCase
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
	public function testGetClassAnnotations(): void
	{
		# annotations
		$loggedIn = new SixtyEightPublishers\SmartNetteComponent\Annotation\LoggedIn();

		$isInRole = new SixtyEightPublishers\SmartNetteComponent\Annotation\IsInRole();
		$isInRole->name = 'foo';

		$isInRole2 = new SixtyEightPublishers\SmartNetteComponent\Annotation\IsInRole();
		$isInRole2->name = 'bar';

		# mocks
		$fooReflection = Mockery::mock(\ReflectionClass::class);
		$barReflection = Mockery::mock(\ReflectionClass::class);
		$reader = Mockery::mock(Doctrine\Common\Annotations\Reader::class);

		$fooReflection->shouldReceive('getName')->andReturn('Foo');
		$fooReflection->shouldReceive('getParentClass')->andReturn(FALSE);

		$barReflection->shouldReceive('getName')->andReturn('Bar');
		$barReflection->shouldReceive('getParentClass')->andReturn($fooReflection);

		$reader->shouldReceive('getClassAnnotations')->with($fooReflection)->andReturn([
			$loggedIn,
			$isInRole,
		]);

		$reader->shouldReceive('getClassAnnotations')->with($barReflection)->andReturn([
			$isInRole2,
		]);

		# asserts

		$doctrineAnnotationReader = new SixtyEightPublishers\SmartNetteComponent\Reader\DoctrineAnnotationReader($reader);

		Tester\Assert::equal([
			new SixtyEightPublishers\SmartNetteComponent\Reader\ClassAnnotation($fooReflection, $loggedIn),
			new SixtyEightPublishers\SmartNetteComponent\Reader\ClassAnnotation($fooReflection, $isInRole),
		], $doctrineAnnotationReader->getClassAnnotations($fooReflection));

		Tester\Assert::equal([
			new SixtyEightPublishers\SmartNetteComponent\Reader\ClassAnnotation($barReflection, $isInRole2),
			new SixtyEightPublishers\SmartNetteComponent\Reader\ClassAnnotation($fooReflection, $loggedIn),
			new SixtyEightPublishers\SmartNetteComponent\Reader\ClassAnnotation($fooReflection, $isInRole),
		], $doctrineAnnotationReader->getClassAnnotations($barReflection));

		Tester\Assert::equal([
			new SixtyEightPublishers\SmartNetteComponent\Reader\ClassAnnotation($barReflection, $isInRole2),
		], $doctrineAnnotationReader->getClassAnnotations($barReflection, 'Bar'));
	}

	/**
	 * @return void
	 */
	public function testGetMethodAnnotations(): void
	{
		# annotations
		$loggedIn = new SixtyEightPublishers\SmartNetteComponent\Annotation\LoggedIn();

		$isInRole = new SixtyEightPublishers\SmartNetteComponent\Annotation\IsInRole();
		$isInRole->name = 'foo';

		# mocks
		$reflection = Mockery::mock(\ReflectionMethod::class);
		$reader = Mockery::mock(Doctrine\Common\Annotations\Reader::class);

		$reader->shouldReceive('getMethodAnnotations')->once()->with($reflection)->andReturn([
			$loggedIn,
			$isInRole,
		]);

		# asserts
		$doctrineAnnotationReader = new SixtyEightPublishers\SmartNetteComponent\Reader\DoctrineAnnotationReader($reader);

		Tester\Assert::equal([
			$loggedIn,
			$isInRole,
		], $doctrineAnnotationReader->getMethodAnnotations($reflection));
	}
}

(new DoctrineAnnotationReaderTest())->run();

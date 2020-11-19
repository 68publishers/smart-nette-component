<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Cases\Reader;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use ReflectionClass;
use ReflectionMethod;
use Doctrine\Common\Annotations\Reader;
use SixtyEightPublishers\SmartNetteComponent\Annotation\LoggedIn;
use SixtyEightPublishers\SmartNetteComponent\Annotation\IsInRole;
use SixtyEightPublishers\SmartNetteComponent\Reader\ClassAnnotation;
use SixtyEightPublishers\SmartNetteComponent\Reader\DoctrineAnnotationReader;

require __DIR__ . '/../../bootstrap.php';

final class DoctrineAnnotationReaderTest extends TestCase
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
		$loggedIn = new LoggedIn();
		$isInRole = new IsInRole();
		$isInRole2 = new IsInRole();

		$isInRole->name = 'foo';
		$isInRole2->name = 'bar';

		# mocks
		$fooReflection = Mockery::mock(ReflectionClass::class);
		$barReflection = Mockery::mock(ReflectionClass::class);
		$reader = Mockery::mock(Reader::class);

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

		$doctrineAnnotationReader = new DoctrineAnnotationReader($reader);

		Assert::equal([
			new ClassAnnotation($fooReflection, $loggedIn),
			new ClassAnnotation($fooReflection, $isInRole),
		], $doctrineAnnotationReader->getClassAnnotations($fooReflection));

		Assert::equal([
			new ClassAnnotation($fooReflection, $loggedIn),
			new ClassAnnotation($fooReflection, $isInRole),
			new ClassAnnotation($barReflection, $isInRole2),
		], $doctrineAnnotationReader->getClassAnnotations($barReflection));

		Assert::equal([
			new ClassAnnotation($barReflection, $isInRole2),
		], $doctrineAnnotationReader->getClassAnnotations($barReflection, 'Bar'));
	}

	/**
	 * @return void
	 */
	public function testGetMethodAnnotations(): void
	{
		# annotations
		$loggedIn = new LoggedIn();
		$isInRole = new IsInRole();

		$isInRole->name = 'foo';

		# mocks
		$reflection = Mockery::mock(ReflectionMethod::class);
		$reader = Mockery::mock(Reader::class);

		$reader->shouldReceive('getMethodAnnotations')->once()->with($reflection)->andReturn([
			$loggedIn,
			$isInRole,
		]);

		# asserts
		$doctrineAnnotationReader = new DoctrineAnnotationReader($reader);

		Assert::equal([
			$loggedIn,
			$isInRole,
		], $doctrineAnnotationReader->getMethodAnnotations($reflection));
	}
}

(new DoctrineAnnotationReaderTest())->run();

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Reader;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\SmartNetteComponent\Reader\AttributeInfo;
use SixtyEightPublishers\SmartNetteComponent\Reader\AttributeReaderInterface;
use SixtyEightPublishers\SmartNetteComponent\Reader\RuntimeCachedAttributeReader;

require __DIR__ . '/../bootstrap.php';

final class RuntimeCachedAttributeReaderTest extends TestCase
{
	public function testGetClassAttributes(): void
	{
		$attributesA = [
			Mockery::mock(AttributeInfo::class),
			Mockery::mock(AttributeInfo::class),
		];
		$attributesB = [
			Mockery::mock(AttributeInfo::class),
			Mockery::mock(AttributeInfo::class),
		];
		$innerReader = Mockery::mock(AttributeReaderInterface::class);

		$innerReader->shouldReceive('getClassAttributes')
			->once()
			->with('A', null)
			->andReturn($attributesA);

		$innerReader->shouldReceive('getClassAttributes')
			->once()
			->with('A', 'B')
			->andReturn($attributesB);

		$reader = new RuntimeCachedAttributeReader($innerReader);

		Assert::same($attributesA, $reader->getClassAttributes('A'));
		Assert::same($attributesB, $reader->getClassAttributes('A', 'B'));

		// cached calls
		Assert::same($attributesA, $reader->getClassAttributes('A'));
		Assert::same($attributesB, $reader->getClassAttributes('A', 'B'));
	}

	public function testGetMethodAttributes(): void
	{
		$attributesA = [
			Mockery::mock(AttributeInfo::class),
			Mockery::mock(AttributeInfo::class),
		];
		$attributesB = [
			Mockery::mock(AttributeInfo::class),
			Mockery::mock(AttributeInfo::class),
		];
		$innerReader = Mockery::mock(AttributeReaderInterface::class);

		$innerReader->shouldReceive('getMethodAttributes')
			->once()
			->with('A', 'actionDefault', null)
			->andReturn($attributesA);

		$innerReader->shouldReceive('getMethodAttributes')
			->once()
			->with('A', 'actionDefault', 'B')
			->andReturn($attributesB);

		$reader = new RuntimeCachedAttributeReader($innerReader);

		Assert::same($attributesA, $reader->getMethodAttributes('A', 'actionDefault'));
		Assert::same($attributesB, $reader->getMethodAttributes('A', 'actionDefault', 'B'));

		// cached calls
		Assert::same($attributesA, $reader->getMethodAttributes('A', 'actionDefault'));
		Assert::same($attributesB, $reader->getMethodAttributes('A', 'actionDefault', 'B'));
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}
}

(new RuntimeCachedAttributeReaderTest())->run();

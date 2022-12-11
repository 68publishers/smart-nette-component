<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Reader;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\SmartNetteComponent\Reader\AttributeReader;

require __DIR__ . '/../bootstrap.php';

final class AttributeReaderTest extends TestCase
{
	use DataProvidersTrait;

	private AttributeReader $reader;

	private AttributeReader $readerWithPrototypes;

	/**
	 * @dataProvider getClassAttributesData
	 */
	public function testGetClassAttributesFromMultipleHierarchies(string $classname, array $attributes): void
	{
		Assert::equal($attributes, $this->reader->getClassAttributes($classname));
	}

	/**
	 * @dataProvider getClassAttributePrototypesData
	 */
	public function testGetClassAttributePrototypesFromMultipleHierarchies(string $classname, array $attributes): void
	{
		Assert::equal($attributes, $this->readerWithPrototypes->getClassAttributes($classname));
	}

	/**
	 * @dataProvider getClassAttributesDataWithStopBeforeOption
	 */
	public function testGetClassAttributesWithStopBeforeOption(string $classname, string $stopBefore, array $attributes): void
	{
		Assert::equal($attributes, $this->reader->getClassAttributes($classname, $stopBefore));
	}

	/**
	 * @dataProvider getClassAttributePrototypesDataWithStopBeforeOption
	 */
	public function testGetClassAttributePrototypesWithStopBeforeOption(string $classname, string $stopBefore, array $attributes): void
	{
		Assert::equal($attributes, $this->readerWithPrototypes->getClassAttributes($classname, $stopBefore));
	}

	/**
	 * @dataProvider getMethodAttributesData
	 */
	public function testGetMethodAttributesFromMultipleHierarchies(string $classname, string $method, array $attributes): void
	{
		Assert::equal($attributes, $this->reader->getMethodAttributes($classname, $method));
	}

	/**
	 * @dataProvider getMethodAttributePrototypesData
	 */
	public function testGetMethodAttributePrototypesFromMultipleHierarchies(string $classname, string $method, array $attributes): void
	{
		Assert::equal($attributes, $this->readerWithPrototypes->getMethodAttributes($classname, $method));
	}

	/**
	 * @dataProvider getMethodAttributesDataWithStopBeforeOption
	 */
	public function testGetMethodAttributesWithStopBeforeOption(string $classname, string $method, string $stopBefore, array $attributes): void
	{
		Assert::equal($attributes, $this->reader->getMethodAttributes($classname, $method, $stopBefore));
	}

	/**
	 * @dataProvider getMethodAttributePrototypesDataWithStopBeforeOption
	 */
	public function testGetMethodAttributePrototypesWithStopBeforeOption(string $classname, string $method, string $stopBefore, array $attributes): void
	{
		Assert::equal($attributes, $this->readerWithPrototypes->getMethodAttributes($classname, $method, $stopBefore));
	}

	protected function setUp(): void
	{
		$this->reader = new AttributeReader();
		$this->readerWithPrototypes = AttributeReader::createWithAttributePrototypes();
	}
}

(new AttributeReaderTest())->run();

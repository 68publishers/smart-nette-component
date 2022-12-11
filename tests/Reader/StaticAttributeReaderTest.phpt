<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Reader;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\SmartNetteComponent\Attribute\InRole;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedIn;
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedOut;
use SixtyEightPublishers\SmartNetteComponent\Reader\AttributesMap;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\AComponent;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\APresenter;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\BComponent;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\BPresenter;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\CPresenter;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\DPresenter;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\EPresenter;
use SixtyEightPublishers\SmartNetteComponent\Reader\StaticAttributeReader;

require __DIR__ . '/../bootstrap.php';

final class StaticAttributeReaderTest extends TestCase
{
	use DataProvidersTrait;

	private StaticAttributeReader $reader;

	/**
	 * @dataProvider getClassAttributesData
	 */
	public function testGetClassAttributesFromMultipleHierarchies(string $classname, array $attributes): void
	{
		Assert::equal($attributes, $this->reader->getClassAttributes($classname));
	}

	/**
	 * @dataProvider getClassAttributesDataWithStopBeforeOption
	 */
	public function testGetClassAttributesWithStopBeforeOption(string $classname, string $stopBefore, array $attributes): void
	{
		Assert::equal($attributes, $this->reader->getClassAttributes($classname, $stopBefore));
	}

	/**
	 * @dataProvider getMethodAttributesData
	 */
	public function testGetMethodAttributesFromMultipleHierarchies(string $classname, string $method, array $attributes): void
	{
		Assert::equal($attributes, $this->reader->getMethodAttributes($classname, $method));
	}

	/**
	 * @dataProvider getMethodAttributesDataWithStopBeforeOption
	 */
	public function testGetMethodAttributesWithStopBeforeOption(string $classname, string $method, string $stopBefore, array $attributes): void
	{
		Assert::equal($attributes, $this->reader->getMethodAttributes($classname, $method, $stopBefore));
	}

	protected function setUp(): void
	{
		$this->reader = new StaticAttributeReader(new AttributesMap(
			[
				APresenter::class => false,
				BPresenter::class => false,
				CPresenter::class => APresenter::class,
				DPresenter::class => APresenter::class,
				EPresenter::class => CPresenter::class,
				AComponent::class => false,
				BComponent::class => AComponent::class,
			],
			[
				APresenter::class . '::handleSignalA' => [
					new Allowed('resourceA', 'signalA'),
				],
				BPresenter::class . '::class' => [
					new LoggedOut(),
					new InRole('admin'),
				],
				CPresenter::class . '::class' => [
					new LoggedIn(),
				],
				CPresenter::class . '::actionDefault' => [
					new Allowed('resourceC', 'actionC'),
				],
				DPresenter::class . '::actionDefault' => [
					new Allowed('resourceD', 'actionD'),
				],
				DPresenter::class . '::handleSignalD' => [
					new Allowed('resourceD', 'signalD'),
					new LoggedIn(),
				],
				EPresenter::class . '::class' => [
					new InRole('customer'),
				],
				EPresenter::class . '::actionDefault' => [
					new Allowed('resourceE', 'actionE'),
				],
				EPresenter::class . '::handleSignalA' => [
					new Allowed('resourceE', 'signalE'),
				],
				EPresenter::class . '::handleSignalE' => [
					new Allowed('resourceE', 'signalE'),
				],
				AComponent::class . '::handleSignalA' => [
					new InRole('admin'),
				],
				BComponent::class . '::handleSignalA' => [
					new Allowed('resourceB', 'signalA'),
				],
				BComponent::class . '::handleSignalB' => [
					new LoggedIn(),
				],
			]
		));
	}
}

(new StaticAttributeReaderTest())->run();

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Reader;

use Mockery;
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
use SixtyEightPublishers\SmartNetteComponent\Attribute\AttributeInterface;
use SixtyEightPublishers\SmartNetteComponent\Attribute\AttributePrototype;

require __DIR__ . '/../bootstrap.php';

final class AttributesMapTest extends TestCase
{
	public function testNewMapShouldBeReturnedWhenClassAdded(): void
	{
		$map1 = new AttributesMap([], []);
		$map2 = $map1->withClass('A', false);
		$map3 = $map2->withClass('B', 'A');
		$map4 = $map3->withClass('C', 'B');
		$map5 = $map4->withClass('C', false);

		Assert::notSame($map1, $map2);
		Assert::notSame($map2, $map3);
		Assert::notSame($map3, $map4);
		Assert::notSame($map4, $map5);

		Assert::same([], $map1->classHierarchy);

		Assert::same([
			'A' => false,
		], $map2->classHierarchy);

		Assert::same([
			'A' => false,
			'B' => 'A',
		], $map3->classHierarchy);

		Assert::same([
			'A' => false,
			'B' => 'A',
			'C' => 'B',
		], $map4->classHierarchy);

		Assert::same([
			'A' => false,
			'B' => 'A',
			'C' => false,
		], $map5->classHierarchy);
	}

	public function testNewMapShouldBeReturnedWhenClassAttributesAdded(): void
	{
		$attributesA = [
			Mockery::mock(AttributeInterface::class),
			Mockery::mock(AttributeInterface::class),
		];

		$attributesB = [
			Mockery::mock(AttributeInterface::class),
		];

		$map1 = new AttributesMap([], []);
		$map2 = $map1->withClassAttributes('A', $attributesA);
		$map3 = $map2->withClassAttributes('B', $attributesB);
		$map4 = $map3->withClassAttributes('B', []);

		Assert::notSame($map1, $map2);
		Assert::notSame($map2, $map3);
		Assert::notSame($map3, $map4);

		Assert::same([], $map1->attributes);

		Assert::same([
			'A::class' => $attributesA,
		], $map2->attributes);

		Assert::same([
			'A::class' => $attributesA,
			'B::class' => $attributesB,
		], $map3->attributes);

		Assert::same([
			'A::class' => $attributesA,
			'B::class' => [],
		], $map4->attributes);
	}

	public function testNewMapShouldBeReturnedWhenMethodAttributesAdded(): void
	{
		$attributesAHandle1 = [
			Mockery::mock(AttributeInterface::class),
			Mockery::mock(AttributeInterface::class),
		];

		$attributesBHandle1 = [
			Mockery::mock(AttributeInterface::class),
		];

		$attributesBHandle2 = [
			Mockery::mock(AttributeInterface::class),
			Mockery::mock(AttributeInterface::class),
		];

		$map1 = new AttributesMap([], []);
		$map2 = $map1->withMethodAttributes('A', 'handle1', $attributesAHandle1);
		$map3 = $map2->withMethodAttributes('B', 'handle1', $attributesBHandle1);
		$map4 = $map3->withMethodAttributes('B', 'handle2', $attributesBHandle2);
		$map5 = $map4->withMethodAttributes('B', 'handle1', []);

		Assert::notSame($map1, $map2);
		Assert::notSame($map2, $map3);
		Assert::notSame($map3, $map4);
		Assert::notSame($map4, $map5);

		Assert::same([], $map1->attributes);

		Assert::same([
			'A::handle1' => $attributesAHandle1,
		], $map2->attributes);

		Assert::same([
			'A::handle1' => $attributesAHandle1,
			'B::handle1' => $attributesBHandle1,
		], $map3->attributes);

		Assert::same([
			'A::handle1' => $attributesAHandle1,
			'B::handle1' => $attributesBHandle1,
			'B::handle2' => $attributesBHandle2,
		], $map4->attributes);

		Assert::same([
			'A::handle1' => $attributesAHandle1,
			'B::handle1' => [],
			'B::handle2' => $attributesBHandle2,
		], $map5->attributes);
	}

	/**
	 * @dataProvider getClassList
	 */
	public function testMapShouldBeCreatedFromClassList(array $classList): void
	{
		$map = AttributesMap::createFromClassList($classList);

		Assert::equal([
			APresenter::class => false,
			BPresenter::class => false,
			CPresenter::class => APresenter::class,
			DPresenter::class => APresenter::class,
			EPresenter::class => CPresenter::class,
			AComponent::class => false,
			BComponent::class => AComponent::class,
		], $map->classHierarchy);

		Assert::equal([
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
		], $map->attributes);
	}

	/**
	 * @dataProvider getClassList
	 */
	public function testMapShouldBeCreatedFromClassListWithPrototypes(array $classList): void
	{
		$map = AttributesMap::createFromClassList($classList, true);

		Assert::equal([
			APresenter::class => false,
			BPresenter::class => false,
			CPresenter::class => APresenter::class,
			DPresenter::class => APresenter::class,
			EPresenter::class => CPresenter::class,
			AComponent::class => false,
			BComponent::class => AComponent::class,
		], $map->classHierarchy);

		Assert::equal([
			APresenter::class . '::handleSignalA' => [
				new AttributePrototype(Allowed::class, ['resourceA', 'signalA']),
			],
			BPresenter::class . '::class' => [
				new AttributePrototype(LoggedOut::class, []),
				new AttributePrototype(InRole::class, ['admin']),
			],
			CPresenter::class . '::class' => [
				new AttributePrototype(LoggedIn::class, []),
			],
			CPresenter::class . '::actionDefault' => [
				new AttributePrototype(Allowed::class, ['resourceC', 'actionC']),
			],
			DPresenter::class . '::actionDefault' => [
				new AttributePrototype(Allowed::class, ['resourceD', 'actionD']),
			],
			DPresenter::class . '::handleSignalD' => [
				new AttributePrototype(Allowed::class, ['resourceD', 'signalD']),
				new AttributePrototype(LoggedIn::class, []),
			],
			EPresenter::class . '::class' => [
				new AttributePrototype(InRole::class, ['customer']),
			],
			EPresenter::class . '::actionDefault' => [
				new AttributePrototype(Allowed::class, ['resourceE', 'actionE']),
			],
			EPresenter::class . '::handleSignalA' => [
				new AttributePrototype(Allowed::class, ['resourceE', 'signalE']),
			],
			EPresenter::class . '::handleSignalE' => [
				new AttributePrototype(Allowed::class, ['resourceE', 'signalE']),
			],
			AComponent::class . '::handleSignalA' => [
				new AttributePrototype(InRole::class, ['admin']),
			],
			BComponent::class . '::handleSignalA' => [
				new AttributePrototype(Allowed::class, ['resourceB', 'signalA']),
			],
			BComponent::class . '::handleSignalB' => [
				new AttributePrototype(LoggedIn::class, []),
			],
		], $map->attributes);
	}

	public function getClassList(): array
	{
		return [
			[[
				# non duplicates in parents
				EPresenter::class,
				DPresenter::class,
				BPresenter::class,
				BComponent::class,
			]],
			[[
				# all - duplicates in parents
				APresenter::class,
				BPresenter::class,
				CPresenter::class,
				DPresenter::class,
				EPresenter::class,
				AComponent::class,
				BComponent::class,
			]],
		];
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}
}

(new AttributesMapTest())->run();

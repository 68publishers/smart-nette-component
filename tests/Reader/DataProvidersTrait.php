<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Reader;

use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use SixtyEightPublishers\SmartNetteComponent\Attribute\InRole;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedIn;
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedOut;
use SixtyEightPublishers\SmartNetteComponent\Reader\AttributeInfo;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\AComponent;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\APresenter;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\BComponent;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\BPresenter;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\CPresenter;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\DPresenter;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\EPresenter;
use SixtyEightPublishers\SmartNetteComponent\Attribute\AttributePrototype;

trait DataProvidersTrait
{
	public function getClassAttributesData(): array
	{
		return [
			[APresenter::class, []],
			[BPresenter::class, [
				new AttributeInfo(BPresenter::class, new LoggedOut()),
				new AttributeInfo(BPresenter::class, new InRole('admin')),
			]],
			[CPresenter::class, [
				new AttributeInfo(CPresenter::class, new LoggedIn()),
			]],
			[DPresenter::class, []],
			[EPresenter::class, [
				new AttributeInfo(CPresenter::class, new LoggedIn()),
				new AttributeInfo(EPresenter::class, new InRole('customer')),
			]],
		];
	}

	public function getClassAttributePrototypesData(): array
	{
		return [
			[APresenter::class, []],
			[BPresenter::class, [
				new AttributeInfo(BPresenter::class, new AttributePrototype(LoggedOut::class, [])),
				new AttributeInfo(BPresenter::class, new AttributePrototype(InRole::class, ['admin'])),
			]],
			[CPresenter::class, [
				new AttributeInfo(CPresenter::class, new AttributePrototype(LoggedIn::class, [])),
			]],
			[DPresenter::class, []],
			[EPresenter::class, [
				new AttributeInfo(CPresenter::class, new AttributePrototype(LoggedIn::class, [])),
				new AttributeInfo(EPresenter::class, new AttributePrototype(InRole::class, ['customer'])),
			]],
		];
	}

	public function getClassAttributesDataWithStopBeforeOption(): array
	{
		return [
			[EPresenter::class, EPresenter::class, [
				new AttributeInfo(EPresenter::class, new InRole('customer')),
			]],
			[EPresenter::class, CPresenter::class, [
				new AttributeInfo(EPresenter::class, new InRole('customer')),
			]],
			[EPresenter::class, APresenter::class, [
				new AttributeInfo(CPresenter::class, new LoggedIn()),
				new AttributeInfo(EPresenter::class, new InRole('customer')),
			]],
			[EPresenter::class, Presenter::class, [
				new AttributeInfo(CPresenter::class, new LoggedIn()),
				new AttributeInfo(EPresenter::class, new InRole('customer')),
			]],
		];
	}

	public function getClassAttributePrototypesDataWithStopBeforeOption(): array
	{
		return [
			[EPresenter::class, EPresenter::class, [
				new AttributeInfo(EPresenter::class, new AttributePrototype(InRole::class, ['customer'])),
			]],
			[EPresenter::class, CPresenter::class, [
				new AttributeInfo(EPresenter::class, new AttributePrototype(InRole::class, ['customer'])),
			]],
			[EPresenter::class, APresenter::class, [
				new AttributeInfo(CPresenter::class, new AttributePrototype(LoggedIn::class, [])),
				new AttributeInfo(EPresenter::class, new AttributePrototype(InRole::class, ['customer'])),
			]],
			[EPresenter::class, Presenter::class, [
				new AttributeInfo(CPresenter::class, new AttributePrototype(LoggedIn::class, [])),
				new AttributeInfo(EPresenter::class, new AttributePrototype(InRole::class, ['customer'])),
			]],
		];
	}

	public function getMethodAttributesData(): array
	{
		return [
			[APresenter::class, 'handleSignalA', [
				new AttributeInfo(APresenter::class, new Allowed('resourceA', 'signalA')),
			]],

			[BPresenter::class, 'handleSignalA', []], # missing method

			[CPresenter::class, 'handleSignalA', [
				new AttributeInfo(APresenter::class, new Allowed('resourceA', 'signalA')),
			]],
			[CPresenter::class, 'actionDefault', [
				new AttributeInfo(CPresenter::class, new Allowed('resourceC', 'actionC')),
			]],

			[DPresenter::class, 'handleSignalA', [
				new AttributeInfo(APresenter::class, new Allowed('resourceA', 'signalA')),
			]],
			[DPresenter::class, 'actionDefault', [
				new AttributeInfo(DPresenter::class, new Allowed('resourceD', 'actionD')),
			]],
			[DPresenter::class, 'handleSignalD', [
				new AttributeInfo(DPresenter::class, new Allowed('resourceD', 'signalD')),
				new AttributeInfo(DPresenter::class, new LoggedIn()),
			]],

			[EPresenter::class, 'handleSignalA', [
				new AttributeInfo(APresenter::class, new Allowed('resourceA', 'signalA')),
				new AttributeInfo(EPresenter::class, new Allowed('resourceE', 'signalE')),
			]],
			[EPresenter::class, 'actionDefault', [
				new AttributeInfo(CPresenter::class, new Allowed('resourceC', 'actionC')),
				new AttributeInfo(EPresenter::class, new Allowed('resourceE', 'actionE')),
			]],
			[EPresenter::class, 'handleSignalE', [
				new AttributeInfo(EPresenter::class, new Allowed('resourceE', 'signalE')),
			]],

			[AComponent::class, 'handleSignalA', [
				new AttributeInfo(AComponent::class, new InRole('admin')),
			]],

			[BComponent::class, 'handleSignalA', [
				new AttributeInfo(AComponent::class, new InRole('admin')),
				new AttributeInfo(BComponent::class, new Allowed('resourceB', 'signalA')),
			]],
			[BComponent::class, 'handleSignalB', [
				new AttributeInfo(BComponent::class, new LoggedIn()),
			]],
		];
	}

	public function getMethodAttributePrototypesData(): array
	{
		return [
			[APresenter::class, 'handleSignalA', [
				new AttributeInfo(APresenter::class, new AttributePrototype(Allowed::class, ['resourceA', 'signalA'])),
			]],

			[BPresenter::class, 'handleSignalA', []], # missing method

			[CPresenter::class, 'handleSignalA', [
				new AttributeInfo(APresenter::class, new AttributePrototype(Allowed::class, ['resourceA', 'signalA'])),
			]],
			[CPresenter::class, 'actionDefault', [
				new AttributeInfo(CPresenter::class, new AttributePrototype(Allowed::class, ['resourceC', 'actionC'])),
			]],

			[DPresenter::class, 'handleSignalA', [
				new AttributeInfo(APresenter::class, new AttributePrototype(Allowed::class, ['resourceA', 'signalA'])),
			]],
			[DPresenter::class, 'actionDefault', [
				new AttributeInfo(DPresenter::class, new AttributePrototype(Allowed::class, ['resourceD', 'actionD'])),
			]],
			[DPresenter::class, 'handleSignalD', [
				new AttributeInfo(DPresenter::class, new AttributePrototype(Allowed::class, ['resourceD', 'signalD'])),
				new AttributeInfo(DPresenter::class, new AttributePrototype(LoggedIn::class, [])),
			]],

			[EPresenter::class, 'handleSignalA', [
				new AttributeInfo(APresenter::class, new AttributePrototype(Allowed::class, ['resourceA', 'signalA'])),
				new AttributeInfo(EPresenter::class, new AttributePrototype(Allowed::class, ['resourceE', 'signalE'])),
			]],
			[EPresenter::class, 'actionDefault', [
				new AttributeInfo(CPresenter::class, new AttributePrototype(Allowed::class, ['resourceC', 'actionC'])),
				new AttributeInfo(EPresenter::class, new AttributePrototype(Allowed::class, ['resourceE', 'actionE'])),
			]],
			[EPresenter::class, 'handleSignalE', [
				new AttributeInfo(EPresenter::class, new AttributePrototype(Allowed::class, ['resourceE', 'signalE'])),
			]],

			[AComponent::class, 'handleSignalA', [
				new AttributeInfo(AComponent::class, new AttributePrototype(InRole::class, ['admin'])),
			]],

			[BComponent::class, 'handleSignalA', [
				new AttributeInfo(AComponent::class, new AttributePrototype(InRole::class, ['admin'])),
				new AttributeInfo(BComponent::class, new AttributePrototype(Allowed::class, ['resourceB', 'signalA'])),
			]],
			[BComponent::class, 'handleSignalB', [
				new AttributeInfo(BComponent::class, new AttributePrototype(LoggedIn::class, [])),
			]],
		];
	}

	public function getMethodAttributesDataWithStopBeforeOption(): array
	{
		return [
			[EPresenter::class, 'handleSignalA', EPresenter::class, [
				new AttributeInfo(EPresenter::class, new Allowed('resourceE', 'signalE')),
			]],
			[EPresenter::class, 'handleSignalA', CPresenter::class, [
				new AttributeInfo(EPresenter::class, new Allowed('resourceE', 'signalE')),
			]],
			[EPresenter::class, 'handleSignalA', APresenter::class, [
				new AttributeInfo(EPresenter::class, new Allowed('resourceE', 'signalE')),
			]],
			[EPresenter::class, 'handleSignalA', Presenter::class, [
				new AttributeInfo(APresenter::class, new Allowed('resourceA', 'signalA')),
				new AttributeInfo(EPresenter::class, new Allowed('resourceE', 'signalE')),
			]],

			[BComponent::class, 'handleSignalA', BComponent::class, [
				new AttributeInfo(BComponent::class, new Allowed('resourceB', 'signalA')),
			]],
			[BComponent::class, 'handleSignalA', AComponent::class, [
				new AttributeInfo(BComponent::class, new Allowed('resourceB', 'signalA')),
			]],
			[BComponent::class, 'handleSignalA', Control::class, [
				new AttributeInfo(AComponent::class, new InRole('admin')),
				new AttributeInfo(BComponent::class, new Allowed('resourceB', 'signalA')),
			]],
		];
	}

	public function getMethodAttributePrototypesDataWithStopBeforeOption(): array
	{
		return [
			[EPresenter::class, 'handleSignalA', EPresenter::class, [
				new AttributeInfo(EPresenter::class, new AttributePrototype(Allowed::class, ['resourceE', 'signalE'])),
			]],
			[EPresenter::class, 'handleSignalA', CPresenter::class, [
				new AttributeInfo(EPresenter::class, new AttributePrototype(Allowed::class, ['resourceE', 'signalE'])),
			]],
			[EPresenter::class, 'handleSignalA', APresenter::class, [
				new AttributeInfo(EPresenter::class, new AttributePrototype(Allowed::class, ['resourceE', 'signalE'])),
			]],
			[EPresenter::class, 'handleSignalA', Presenter::class, [
				new AttributeInfo(APresenter::class, new AttributePrototype(Allowed::class, ['resourceA', 'signalA'])),
				new AttributeInfo(EPresenter::class, new AttributePrototype(Allowed::class, ['resourceE', 'signalE'])),
			]],

			[BComponent::class, 'handleSignalA', BComponent::class, [
				new AttributeInfo(BComponent::class, new AttributePrototype(Allowed::class, ['resourceB', 'signalA'])),
			]],
			[BComponent::class, 'handleSignalA', AComponent::class, [
				new AttributeInfo(BComponent::class, new AttributePrototype(Allowed::class, ['resourceB', 'signalA'])),
			]],
			[BComponent::class, 'handleSignalA', Control::class, [
				new AttributeInfo(AComponent::class, new AttributePrototype(InRole::class, ['admin'])),
				new AttributeInfo(BComponent::class, new AttributePrototype(Allowed::class, ['resourceB', 'signalA'])),
			]],
		];
	}
}

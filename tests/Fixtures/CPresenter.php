<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures;

use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedIn;

#[LoggedIn]
abstract class CPresenter extends APresenter
{
	#[Allowed('resourceC', 'actionC')]
	public function actionDefault(): void
	{
	}
}

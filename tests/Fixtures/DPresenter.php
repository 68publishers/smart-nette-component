<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures;

use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedIn;

final class DPresenter extends APresenter
{
	#[Allowed('resourceD', 'actionD')]
	public function actionDefault(): void
	{
	}

	#[Allowed('resourceD', 'signalD')]
	#[LoggedIn]
	public function handleSignalD(): void
	{
	}
}

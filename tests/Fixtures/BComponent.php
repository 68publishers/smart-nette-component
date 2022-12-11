<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures;

use Nette\Application\Attributes\CrossOrigin;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedIn;

final class BComponent extends AComponent
{
	#[Allowed('resourceB', 'signalA')]
	#[CrossOrigin]
	public function handleSignalA(): void
	{
	}

	#[LoggedIn]
	public function handleSignalB(): void
	{
	}
}

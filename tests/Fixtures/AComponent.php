<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures;

use Nette\Application\UI\Control;
use SixtyEightPublishers\SmartNetteComponent\Attribute\InRole;
use SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\Application\AuthorizationTrait;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizatorAwareInterface;

class AComponent extends Control implements ComponentAuthorizatorAwareInterface
{
	use AuthorizationTrait;

	#[InRole('admin')]
	public function handleSignalA(): void
	{
	}
}

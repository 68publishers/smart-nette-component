<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures;

use Nette\Application\UI\Presenter;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\Application\AuthorizationTrait;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizatorAwareInterface;

abstract class APresenter extends Presenter implements ComponentAuthorizatorAwareInterface
{
	use AuthorizationTrait;

	#[Allowed('resourceA', 'signalA')]
	public function handleSignalA(): void
	{
	}
}

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures;

use Nette\Application\UI\Template;
use Nette\Application\Attributes\CrossOrigin;
use Nette\Application\Responses\VoidResponse;
use SixtyEightPublishers\SmartNetteComponent\Attribute\InRole;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[InRole('customer')]
final class EPresenter extends CPresenter
{
	#[Allowed('resourceE', 'actionE')]
	public function actionDefault(): void
	{
	}

	#[Allowed('resourceE', 'signalE')]
	#[CrossOrigin]
	public function handleSignalA(): void
	{
	}

	#[Allowed('resourceE', 'signalE')]
	public function handleSignalE(): void
	{
	}

	public function sendTemplate(?Template $template = null): never
	{
		$this->sendResponse(new VoidResponse());
	}
}

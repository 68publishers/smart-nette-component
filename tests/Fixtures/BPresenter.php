<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures;

use Nette\Application\Request;
use Nette\Application\Response;
use Nette\Application\IPresenter;
use Nette\Application\Responses\VoidResponse;
use SixtyEightPublishers\SmartNetteComponent\Attribute\InRole;
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedOut;
use SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\Application\AuthorizationTrait;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizatorAwareInterface;

#[LoggedOut]
#[InRole('admin')]
final class BPresenter implements IPresenter, ComponentAuthorizatorAwareInterface
{
	use AuthorizationTrait;

	public function run(Request $request): Response
	{
		return new VoidResponse();
	}
}

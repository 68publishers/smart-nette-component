<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Authorization\Handler;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use Nette\Security\User;
use SixtyEightPublishers\SmartNetteComponent\Attribute\InRole;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\Handler\InRoleHandler;
use SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException;

require __DIR__ . '/../../bootstrap.php';

final class InRoleHandlerTest extends TestCase
{
	public function testHandlerCanNotHandleWrongRule(): void
	{
		$handler = new InRoleHandler(Mockery::mock(User::class));

		Assert::false($handler->canHandle(Mockery::mock(RuleInterface::class)));
	}

	public function testHandlerCanHandleRule(): void
	{
		$handler = new InRoleHandler(Mockery::mock(User::class));

		Assert::true($handler->canHandle(new InRole('customer')));
	}

	public function testSuccessfulHandlerInvocation(): void
	{
		$user = Mockery::mock(User::class);

		$user->shouldReceive('isInRole')
			->once()
			->with('customer')
			->andReturn(true);

		$handler = new InRoleHandler($user);

		Assert::noError(static fn () => $handler(new InRole('customer')));
	}

	public function testFailedHandlerInvocation(): void
	{
		$user = Mockery::mock(User::class);

		$user->shouldReceive('isInRole')
			->once()
			->with('customer')
			->andReturn(false);

		$handler = new InRoleHandler($user);

		Assert::exception(
			static fn () => $handler(new InRole('customer')),
			ForbiddenRequestException::class
		);
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}
}

(new InRoleHandlerTest())->run();

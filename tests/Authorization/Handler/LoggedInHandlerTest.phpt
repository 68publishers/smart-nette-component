<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Authorization\Handler;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use Nette\Security\User;
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedIn;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleInterface;
use SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException;
use SixtyEightPublishers\SmartNetteComponent\Authorization\Handler\LoggedInHandler;

require __DIR__ . '/../../bootstrap.php';

final class LoggedInHandlerTest extends TestCase
{
	public function testHandlerCanNotHandleWrongRule(): void
	{
		$handler = new LoggedInHandler(Mockery::mock(User::class));

		Assert::false($handler->canHandle(Mockery::mock(RuleInterface::class)));
	}

	public function testHandlerCanHandleRule(): void
	{
		$handler = new LoggedInHandler(Mockery::mock(User::class));

		Assert::true($handler->canHandle(new LoggedIn()));
	}

	public function testSuccessfulHandlerInvocation(): void
	{
		$user = Mockery::mock(User::class);

		$user->shouldReceive('isLoggedIn')
			->once()
			->andReturn(true);

		$handler = new LoggedInHandler($user);

		Assert::noError(static fn () => $handler(new LoggedIn()));
	}

	public function testFailedHandlerInvocation(): void
	{
		$user = Mockery::mock(User::class);

		$user->shouldReceive('isLoggedIn')
			->once()
			->andReturn(false);

		$handler = new LoggedInHandler($user);

		Assert::exception(
			static fn () => $handler(new LoggedIn()),
			ForbiddenRequestException::class
		);
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}
}

(new LoggedInHandlerTest())->run();

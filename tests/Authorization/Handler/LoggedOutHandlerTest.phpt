<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Authorization\Handler;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use Nette\Security\User;
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedOut;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleInterface;
use SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException;
use SixtyEightPublishers\SmartNetteComponent\Authorization\Handler\LoggedOutHandler;

require __DIR__ . '/../../bootstrap.php';

final class LoggedOutHandlerTest extends TestCase
{
	public function testHandlerCanNotHandleWrongRule(): void
	{
		$handler = new LoggedOutHandler(Mockery::mock(User::class));

		Assert::false($handler->canHandle(Mockery::mock(RuleInterface::class)));
	}

	public function testHandlerCanHandleRule(): void
	{
		$handler = new LoggedOutHandler(Mockery::mock(User::class));

		Assert::true($handler->canHandle(new LoggedOut()));
	}

	public function testSuccessfulHandlerInvocation(): void
	{
		$user = Mockery::mock(User::class);

		$user->shouldReceive('isLoggedIn')
			->once()
			->andReturn(false);

		$handler = new LoggedOutHandler($user);

		Assert::noError(static fn () => $handler(new LoggedOut()));
	}

	public function testFailedHandlerInvocation(): void
	{
		$user = Mockery::mock(User::class);

		$user->shouldReceive('isLoggedIn')
			->once()
			->andReturn(true);

		$handler = new LoggedOutHandler($user);

		Assert::exception(
			static fn () => $handler(new LoggedOut()),
			ForbiddenRequestException::class
		);
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}
}

(new LoggedOutHandlerTest())->run();

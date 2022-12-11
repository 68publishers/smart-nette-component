<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Authorization\Handler;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use Nette\Security\User;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleInterface;
use SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException;
use SixtyEightPublishers\SmartNetteComponent\Authorization\Handler\AllowedHandler;

require __DIR__ . '/../../bootstrap.php';

final class AllowedHandlerTest extends TestCase
{
	public function testHandlerCanNotHandleWrongRule(): void
	{
		$handler = new AllowedHandler(Mockery::mock(User::class));

		Assert::false($handler->canHandle(Mockery::mock(RuleInterface::class)));
	}

	public function testHandlerCanHandleRule(): void
	{
		$handler = new AllowedHandler(Mockery::mock(User::class));

		Assert::true($handler->canHandle(new Allowed('resource', 'privilege')));
	}

	public function testSuccessfulHandlerInvocation(): void
	{
		$user = Mockery::mock(User::class);

		$user->shouldReceive('isAllowed')
			->once()
			->with('resource', 'privilege')
			->andReturn(true);

		$handler = new AllowedHandler($user);

		Assert::noError(static fn () => $handler(new Allowed('resource', 'privilege')));
	}

	public function testFailedHandlerInvocation(): void
	{
		$user = Mockery::mock(User::class);

		$user->shouldReceive('isAllowed')
			->once()
			->with('resource', 'privilege')
			->andReturn(false);

		$handler = new AllowedHandler($user);

		Assert::exception(
			static fn () => $handler(new Allowed('resource', 'privilege')),
			ForbiddenRequestException::class
		);
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}
}

(new AllowedHandlerTest())->run();

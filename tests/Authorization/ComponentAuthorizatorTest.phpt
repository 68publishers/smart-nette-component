<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Authorization;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleHandlerInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizator;
use SixtyEightPublishers\SmartNetteComponent\Authorization\AuthorizationRulesProviderInterface;

require __DIR__ . '/../bootstrap.php';

final class ComponentAuthorizatorTest extends TestCase
{
	public function testPresenterShouldBeChecked(): void
	{
		$rule1 = Mockery::mock(RuleInterface::class);
		$rule2 = Mockery::mock(RuleInterface::class);
		$provider = Mockery::mock(AuthorizationRulesProviderInterface::class);
		$handler = Mockery::mock(RuleHandlerInterface::class);

		$provider->shouldReceive('provideForPresenter')
			->once()
			->with('A')
			->andReturn([$rule1, $rule2]);

		$handler->shouldReceive('__invoke')
			->once()
			->with($rule1)
			->andReturns();

		$handler->shouldReceive('__invoke')
			->once()
			->with($rule2)
			->andReturns();

		$authorizator = new ComponentAuthorizator($provider, $handler);

		Assert::noError(static fn () => $authorizator->checkPresenter('A'));
	}

	public function testActionShouldBeChecked(): void
	{
		$rule1 = Mockery::mock(RuleInterface::class);
		$rule2 = Mockery::mock(RuleInterface::class);
		$provider = Mockery::mock(AuthorizationRulesProviderInterface::class);
		$handler = Mockery::mock(RuleHandlerInterface::class);

		$provider->shouldReceive('provideForAction')
			->once()
			->with('A', 'default')
			->andReturn([$rule1, $rule2]);

		$handler->shouldReceive('__invoke')
			->once()
			->with($rule1)
			->andReturns();

		$handler->shouldReceive('__invoke')
			->once()
			->with($rule2)
			->andReturns();

		$authorizator = new ComponentAuthorizator($provider, $handler);

		Assert::noError(static fn () => $authorizator->checkAction('A', 'default'));
	}

	public function testSignalShouldBeChecked(): void
	{
		$rule1 = Mockery::mock(RuleInterface::class);
		$rule2 = Mockery::mock(RuleInterface::class);
		$provider = Mockery::mock(AuthorizationRulesProviderInterface::class);
		$handler = Mockery::mock(RuleHandlerInterface::class);

		$provider->shouldReceive('provideForSignal')
			->once()
			->with('A', 'foo')
			->andReturn([$rule1, $rule2]);

		$handler->shouldReceive('__invoke')
			->once()
			->with($rule1)
			->andReturns();

		$handler->shouldReceive('__invoke')
			->once()
			->with($rule2)
			->andReturns();

		$authorizator = new ComponentAuthorizator($provider, $handler);

		Assert::noError(static fn () => $authorizator->checkSignal('A', 'foo'));
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}
}

(new ComponentAuthorizatorTest())->run();

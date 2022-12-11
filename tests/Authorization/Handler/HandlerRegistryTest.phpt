<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Authorization\Handler;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use InvalidArgumentException;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleHandlerInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\Handler\HandlerRegistry;

require __DIR__ . '/../../bootstrap.php';

final class HandlerRegistryTest extends TestCase
{
	public function testExceptionShouldBeThrownIfNoHandlersRegistered(): void
	{
		$registry = new HandlerRegistry([]);

		Assert::exception(
			static fn () => $registry(Mockery::mock(RuleInterface::class)),
			InvalidArgumentException::class,
			'Can\'t handle rule of type %A%RuleInterface.'
		);
	}

	public function testExceptionShouldBeThrownIfHandlerForRuleNotRegistered(): void
	{
		$rule = Mockery::mock(RuleInterface::class);
		$handler1 = Mockery::mock(RuleHandlerInterface::class);
		$handler2 = Mockery::mock(RuleHandlerInterface::class);

		$handler1->shouldReceive('canHandle')
			->once()
			->with($rule)
			->andReturn(false);

		$handler2->shouldReceive('canHandle')
			->once()
			->with($rule)
			->andReturn(false);

		$registry = new HandlerRegistry([$handler1, $handler2]);

		Assert::exception(
			static fn () => $registry($rule),
			InvalidArgumentException::class,
			'Can\'t handle rule of type %A%RuleInterface.'
		);
	}

	public function testRuleShouldBeHandled(): void
	{
		$rule = Mockery::mock(RuleInterface::class);
		$handler1 = Mockery::mock(RuleHandlerInterface::class);
		$handler2 = Mockery::mock(RuleHandlerInterface::class);
		$handler3 = Mockery::mock(RuleHandlerInterface::class);

		$handler1->shouldReceive('canHandle')
			->once()
			->with($rule)
			->andReturn(false);

		$handler2->shouldReceive('canHandle')
			->once()
			->with($rule)
			->andReturn(true);

		$handler2->shouldReceive('__invoke')
			->once()
			->with($rule)
			->andReturnNull();

		$registry = new HandlerRegistry([$handler1, $handler2, $handler3]);

		$registry($rule);
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}
}

(new HandlerRegistryTest())->run();

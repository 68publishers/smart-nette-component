<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Authorization;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use InvalidArgumentException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\APresenter;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleInterface;
use SixtyEightPublishers\SmartNetteComponent\Attribute\AttributeInterface;
use SixtyEightPublishers\SmartNetteComponent\Reader\AttributeReaderInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\AttributeBasedAuthorizationRulesProvider;

require __DIR__ . '/../bootstrap.php';

final class AttributeBasedAuthorizationRulesProviderTest extends TestCase
{
	public function testExceptionShouldBeThrownWhileProvidingRulesForPresenterIfClassnameIsNotPresenter(): void
	{
		$provider = new AttributeBasedAuthorizationRulesProvider(Mockery::mock(AttributeReaderInterface::class));

		Assert::exception(
			static fn () => $provider->provideForPresenter(self::class),
			InvalidArgumentException::class,
			'Class %A%AttributeBasedAuthorizationRulesProviderTest is not a Presenter.'
		);
	}

	public function testExceptionShouldBeThrownWhileProvidingRulesForActionIfClassnameIsNotPresenter(): void
	{
		$provider = new AttributeBasedAuthorizationRulesProvider(Mockery::mock(AttributeReaderInterface::class));

		Assert::exception(
			static fn () => $provider->provideForAction(self::class, 'default'),
			InvalidArgumentException::class,
			'Class %A%AttributeBasedAuthorizationRulesProviderTest is not a Presenter.'
		);
	}

	public function testExceptionShouldBeThrownWhileProvidingRulesForSignalIfClassnameIsNotControl(): void
	{
		$provider = new AttributeBasedAuthorizationRulesProvider(Mockery::mock(AttributeReaderInterface::class));

		Assert::exception(
			static fn () => $provider->provideForSignal(self::class, 'foo'),
			InvalidArgumentException::class,
			'Class %A%AttributeBasedAuthorizationRulesProviderTest is not a Control or Presenter.'
		);
	}

	public function testRulesForPresenterShouldBeProvided(): void
	{
		$reader = Mockery::mock(AttributeReaderInterface::class);
		$attribute1 = Mockery::mock(AttributeInterface::class, RuleInterface::class);
		$attribute2 = Mockery::mock(AttributeInterface::class);
		$attribute3 = Mockery::mock(AttributeInterface::class, RuleInterface::class);

		$reader->shouldReceive('getClassAttributes')
			->once()
			->with(APresenter::class, Presenter::class)
			->andReturn([$attribute1, $attribute2, $attribute3]);

		$provider = new AttributeBasedAuthorizationRulesProvider($reader);

		Assert::same([$attribute1, $attribute3], $provider->provideForPresenter(APresenter::class));
	}

	public function testRulesForActionShouldBeProvided(): void
	{
		$reader = Mockery::mock(AttributeReaderInterface::class);
		$attribute1 = Mockery::mock(AttributeInterface::class, RuleInterface::class);
		$attribute2 = Mockery::mock(AttributeInterface::class);
		$attribute3 = Mockery::mock(AttributeInterface::class, RuleInterface::class);

		$reader->shouldReceive('getMethodAttributes')
			->once()
			->with(APresenter::class, 'actionDefault', Presenter::class)
			->andReturn([$attribute1, $attribute2, $attribute3]);

		$provider = new AttributeBasedAuthorizationRulesProvider($reader);

		Assert::same([$attribute1, $attribute3], $provider->provideForAction(APresenter::class, 'default'));
	}

	public function testRulesForSignalShouldBeProvided(): void
	{
		$reader = Mockery::mock(AttributeReaderInterface::class);
		$attribute1 = Mockery::mock(AttributeInterface::class, RuleInterface::class);
		$attribute2 = Mockery::mock(AttributeInterface::class);
		$attribute3 = Mockery::mock(AttributeInterface::class, RuleInterface::class);

		$reader->shouldReceive('getMethodAttributes')
			->once()
			->with(APresenter::class, 'handleFoo', Control::class)
			->andReturn([$attribute1, $attribute2, $attribute3]);

		$provider = new AttributeBasedAuthorizationRulesProvider($reader);

		Assert::same([$attribute1, $attribute3], $provider->provideForSignal(APresenter::class, 'foo'));
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}
}

(new AttributeBasedAuthorizationRulesProviderTest())->run();

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Bridge\Nette\Application;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use Nette\Application\Request;
use Nette\Application\IPresenter;
use Nette\Application\UI\Presenter;
use Nette\Application\IPresenterFactory;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\BComponent;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\EPresenter;
use SixtyEightPublishers\SmartNetteComponent\Tests\Bridge\Nette\DI\ContainerFactory;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\BComponentFactoryInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizatorInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizatorAwareInterface;
use function assert;

require __DIR__ . '/../../../bootstrap.php';

final class AuthorizationTraitTest extends TestCase
{
	public function testPresenterAction(): void
	{
		$presenter = $this->getPresenter('Tests:E');
		assert($presenter instanceof ComponentAuthorizatorAwareInterface);
		$componentAuthorizator = Mockery::mock(ComponentAuthorizatorInterface::class);

		$componentAuthorizator->shouldReceive('checkPresenter')
			->once()
			->with(EPresenter::class)
			->andReturns();

		$componentAuthorizator->shouldReceive('checkAction')
			->once()
			->with(EPresenter::class, 'actionDefault')
			->andReturns();

		$presenter->setComponentAuthorizator($componentAuthorizator);
		$presenter->run(new Request('E', 'GET', [Presenter::ACTION_KEY => 'default']));

		# dummy assertion, methods are already checked by mock
		Assert::true(true);
	}

	public function testPresenterSignal(): void
	{
		$presenter = $this->getPresenter('Tests:E');
		assert($presenter instanceof ComponentAuthorizatorAwareInterface);
		$componentAuthorizator = Mockery::mock(ComponentAuthorizatorInterface::class);

		$componentAuthorizator->shouldReceive('checkPresenter')
			->once()
			->with(EPresenter::class)
			->andReturns();

		$componentAuthorizator->shouldReceive('checkAction')
			->once()
			->with(EPresenter::class, 'actionDefault')
			->andReturns();

		$componentAuthorizator->shouldReceive('checkSignal')
			->once()
			->with(EPresenter::class, 'handleSignalA')
			->andReturns();

		$presenter->setComponentAuthorizator($componentAuthorizator);
		$presenter->run(new Request('E', 'GET', [Presenter::ACTION_KEY => 'default', Presenter::SIGNAL_KEY => 'signalA']));

		# dummy assertion, methods are already checked by mock
		Assert::true(true);
	}

	public function testComponentSignal(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config.neon');
		$componentFactory = $container->getByType(BComponentFactoryInterface::class);
		assert($componentFactory instanceof BComponentFactoryInterface);
		$componentAuthorizator = Mockery::mock(ComponentAuthorizatorInterface::class);

		$componentAuthorizator->shouldReceive('checkSignal')
			->once()
			->with(BComponent::class, 'handleSignalA')
			->andReturns();

		$component = $componentFactory->create();

		$component->setComponentAuthorizator($componentAuthorizator);
		$component->signalReceived('signalA');

		# dummy assertion, methods are already checked by mock
		Assert::true(true);
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}

	private function getPresenter(string $name): IPresenter
	{
		$container = ContainerFactory::create(__DIR__ . '/config.neon');
		$presenterFactory = $container->getByType(IPresenterFactory::class);
		assert($presenterFactory instanceof IPresenterFactory);

		$presenter = $presenterFactory->createPresenter($name);
		assert($presenter instanceof Presenter);
		$presenter->autoCanonicalize = false;

		return $presenter;
	}
}

(new AuthorizationTraitTest())->run();

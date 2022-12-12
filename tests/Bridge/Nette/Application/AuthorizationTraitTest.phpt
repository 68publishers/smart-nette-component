<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Bridge\Nette\Application;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use Nette\Application\Request;
use Nette\Application\IPresenter;
use Tester\CodeCoverage\Collector;
use Nette\Application\UI\Presenter;
use Nette\Application\IPresenterFactory;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\BComponent;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\EPresenter;
use SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException;
use SixtyEightPublishers\SmartNetteComponent\Tests\Bridge\Nette\DI\ContainerFactory;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\BComponentFactoryInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizatorInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizatorAwareInterface;
use function assert;

require __DIR__ . '/../../../bootstrap.php';

final class AuthorizationTraitTest extends TestCase
{
	public function testAuthorizedPresenterAction(): void
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

	public function testUnauthorizedPresenterAction(): void
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
			->andThrows(new ForbiddenRequestException(new Allowed('resourceE', 'actionE')));

		$presenter->setComponentAuthorizator($componentAuthorizator);

		Assert::exception(
			static fn () => $presenter->run(new Request('E', 'GET', [Presenter::ACTION_KEY => 'default'])),
			ForbiddenRequestException::class
		);
	}

	public function testAuthorizedPresenterSignal(): void
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

	public function testUnauthorizedPresenterSignal(): void
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
			->andThrows(new ForbiddenRequestException(new Allowed('resourceA', 'signalA')));

		$presenter->setComponentAuthorizator($componentAuthorizator);

		Assert::exception(
			static fn () => $presenter->run(new Request('E', 'GET', [Presenter::ACTION_KEY => 'default', Presenter::SIGNAL_KEY => 'signalA'])),
			ForbiddenRequestException::class
		);
	}

	public function testAuthorizedComponentSignal(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config.authorization.neon');
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

	public function testUnauthorizedComponentSignal(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config.authorization.neon');
		$componentFactory = $container->getByType(BComponentFactoryInterface::class);
		assert($componentFactory instanceof BComponentFactoryInterface);
		$componentAuthorizator = Mockery::mock(ComponentAuthorizatorInterface::class);

		$componentAuthorizator->shouldReceive('checkSignal')
			->once()
			->with(BComponent::class, 'handleSignalA')
			->andThrows(new ForbiddenRequestException(new Allowed('resourceB', 'signalA')));

		$component = $componentFactory->create();

		$component->setComponentAuthorizator($componentAuthorizator);

		Assert::exception(
			static fn () => $component->signalReceived('signalA'),
			ForbiddenRequestException::class
		);
	}

	protected function tearDown(): void
	{
		Mockery::close();

		# save manually partial code coverage to free memory
		if (Collector::isStarted()) {
			Collector::save();
		}
	}

	private function getPresenter(string $name): IPresenter
	{
		$container = ContainerFactory::create(__DIR__ . '/config.authorization.neon');
		$presenterFactory = $container->getByType(IPresenterFactory::class);
		assert($presenterFactory instanceof IPresenterFactory);

		$presenter = $presenterFactory->createPresenter($name);
		assert($presenter instanceof Presenter);
		$presenter->autoCanonicalize = false;

		return $presenter;
	}
}

(new AuthorizationTraitTest())->run();

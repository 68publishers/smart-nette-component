<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Bridge\Nette\Application;

use Tester\Assert;
use Tester\TestCase;
use Nette\DI\Container;
use Nette\Utils\Helpers;
use Nette\Application\IPresenter;
use Tester\CodeCoverage\Collector;
use Nette\Application\UI\Presenter;
use Nette\Application\IPresenterFactory;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\EPresenter;
use SixtyEightPublishers\SmartNetteComponent\Tests\Bridge\Nette\DI\ContainerFactory;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\AComponentFactoryInterface;
use function assert;

require __DIR__ . '/../../../bootstrap.php';

final class TemplateResolverTraitTest extends TestCase
{
	public function testComponentShouldBeRendered(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config.templateResolver.neon');
		$presenter = $this->getPresenter($container, 'Tests:E');
		$componentFactory = $container->getByType(AComponentFactoryInterface::class);
		assert($presenter instanceof EPresenter && $componentFactory instanceof AComponentFactoryInterface);

		$component = $componentFactory->create();
		$presenter->addComponent($component, 'a');

		$this->saveCollector();

		$defaultTemplateOutput = Helpers::capture(static fn () => $component->render());
		$secondTemplateOutput = Helpers::capture(static fn () => $component->renderSecond());

		$this->saveCollector();

		Assert::contains('<p>first</p>', $defaultTemplateOutput);
		Assert::contains('<p>second</p>', $secondTemplateOutput);
	}

	public function testComponentShouldBeRenderedWithAbsoluteFile(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config.templateResolver.neon');
		$presenter = $this->getPresenter($container, 'Tests:E');
		$componentFactory = $container->getByType(AComponentFactoryInterface::class);
		assert($presenter instanceof EPresenter && $componentFactory instanceof AComponentFactoryInterface);

		$component = $componentFactory->create();
		$presenter->addComponent($component, 'a');

		$component->setFile(__DIR__ . '/../../../Fixtures/templates/second.AComponent.latte');

		$this->saveCollector();

		$output = Helpers::capture(static fn () => $component->render());

		$this->saveCollector();

		Assert::contains('<p>second</p>', $output);
	}

	public function testComponentShouldBeRenderedWithRelativeFile(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config.templateResolver.neon');
		$presenter = $this->getPresenter($container, 'Tests:E');
		$componentFactory = $container->getByType(AComponentFactoryInterface::class);
		assert($presenter instanceof EPresenter && $componentFactory instanceof AComponentFactoryInterface);

		$component = $componentFactory->create();
		$presenter->addComponent($component, 'a');

		$component->setRelativeFile('second.AComponent.latte');

		$this->saveCollector();

		$output = Helpers::capture(static fn () => $component->render());

		$this->saveCollector();

		Assert::contains('<p>second</p>', $output);
	}

	private function saveCollector(): void
	{
		# save manually partial code coverage to free memory
		if (Collector::isStarted()) {
			Collector::save();
		}
	}

	private function getPresenter(Container $container, string $name): IPresenter
	{
		$presenterFactory = $container->getByType(IPresenterFactory::class);
		assert($presenterFactory instanceof IPresenterFactory);

		$presenter = $presenterFactory->createPresenter($name);
		assert($presenter instanceof Presenter);
		$presenter->autoCanonicalize = false;

		return $presenter;
	}
}

(new TemplateResolverTraitTest())->run();

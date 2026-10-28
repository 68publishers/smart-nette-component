<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Bridge\Nette\Application;

use Tester\Assert;
use Tester\TestCase;
use Nette\Application\Request;
use Tester\CodeCoverage\Collector;
use Nette\Application\UI\Presenter;
use Nette\Application\IPresenterFactory;
use SixtyEightPublishers\SmartNetteComponent\Attribute\InRole;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedIn;
use SixtyEightPublishers\SmartNetteComponent\Reader\AttributesMap;
use SixtyEightPublishers\SmartNetteComponent\Reader\AttributeReader;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\BComponent;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\EPresenter;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleInterface;
use SixtyEightPublishers\SmartNetteComponent\Reader\StaticAttributeReader;
use SixtyEightPublishers\SmartNetteComponent\Reader\AttributeReaderInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleHandlerInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizator;
use SixtyEightPublishers\SmartNetteComponent\Tests\Bridge\Nette\DI\ContainerFactory;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\BComponentFactoryInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\AttributeBasedAuthorizationRulesProvider;
use function assert;

require __DIR__ . '/../../../bootstrap.php';

/**
 * AuthorizationTrait -> ComponentAuthorizator -> AttributeBasedAuthorizationRulesProvider -> reader, without mocks.
 */
final class AuthorizationIntegrationTest extends TestCase
{
	public function testPresenterRulesShouldBeCheckedWithRuntimeReader(): void
	{
		$this->assertPresenterRules(new AttributeReader());
	}

	public function testPresenterRulesShouldBeCheckedWithStaticReader(): void
	{
		$this->assertPresenterRules(new StaticAttributeReader(AttributesMap::createFromClassList([EPresenter::class])));
	}

	public function testComponentSignalRulesShouldBeCheckedWithRuntimeReader(): void
	{
		$this->assertComponentSignalRules(new AttributeReader());
	}

	public function testComponentSignalRulesShouldBeCheckedWithStaticReader(): void
	{
		$this->assertComponentSignalRules(new StaticAttributeReader(AttributesMap::createFromClassList([BComponent::class])));
	}

	protected function tearDown(): void
	{
		# save manually partial code coverage to free memory
		if (Collector::isStarted()) {
			Collector::save();
		}
	}

	private function assertPresenterRules(AttributeReaderInterface $reader): void
	{
		$handler = $this->createRuleHandler();
		$presenter = $this->createPresenter('Tests:E');
		assert($presenter instanceof EPresenter);

		$presenter->setComponentAuthorizator(new ComponentAuthorizator(new AttributeBasedAuthorizationRulesProvider($reader), $handler));
		$presenter->run(new Request('E', 'GET', [Presenter::ACTION_KEY => 'default', Presenter::SIGNAL_KEY => 'signalA']));

		Assert::equal([
			new LoggedIn(),
			new InRole('customer'),
			new Allowed('resourceC', 'actionC'),
			new Allowed('resourceE', 'actionE'),
			new Allowed('resourceA', 'signalA'),
			new Allowed('resourceE', 'signalE'),
		], $handler->rules);
	}

	private function assertComponentSignalRules(AttributeReaderInterface $reader): void
	{
		$handler = $this->createRuleHandler();
		$container = ContainerFactory::create(__DIR__ . '/config.authorization.neon');
		$componentFactory = $container->getByType(BComponentFactoryInterface::class);
		assert($componentFactory instanceof BComponentFactoryInterface);

		$component = $componentFactory->create();
		$this->createPresenter('Tests:E')->addComponent($component, 'b');

		$component->setComponentAuthorizator(new ComponentAuthorizator(new AttributeBasedAuthorizationRulesProvider($reader), $handler));
		$component->signalReceived('signalA');

		Assert::equal([
			new InRole('admin'),
			new Allowed('resourceB', 'signalA'),
		], $handler->rules);
	}

	private function createPresenter(string $name): Presenter
	{
		$container = ContainerFactory::create(__DIR__ . '/config.authorization.neon');
		$presenterFactory = $container->getByType(IPresenterFactory::class);
		assert($presenterFactory instanceof IPresenterFactory);

		$presenter = $presenterFactory->createPresenter($name);
		assert($presenter instanceof Presenter);
		$presenter->autoCanonicalize = false;

		return $presenter;
	}

	private function createRuleHandler(): RuleHandlerInterface
	{
		return new class implements RuleHandlerInterface {
			/** @var array<RuleInterface> */
			public array $rules = [];

			public function canHandle(RuleInterface $rule): bool
			{
				return true;
			}

			public function __invoke(RuleInterface $rule): void
			{
				$this->rules[] = $rule;
			}
		};
	}
}

(new AuthorizationIntegrationTest())->run();

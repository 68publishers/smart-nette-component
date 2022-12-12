<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\TemplateResolver;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use RuntimeException;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\Metadata;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\AComponent;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\ManualTemplateFileResolver;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\TemplateFileResolverInterface;
use function realpath;

require __DIR__ . '/../bootstrap.php';

final class ManualTemplateFileResolverTest extends TestCase
{
	public function testFallbackResolverShouldBeInvokedWithDefaultType(): void
	{
		$fallback = Mockery::mock(TemplateFileResolverInterface::class);

		$fallback->shouldReceive('resolve')
			->once()
			->with('')
			->andReturns('fallback_path');

		$resolver = new ManualTemplateFileResolver($fallback, new Metadata(AComponent::class, 'AComponent', __DIR__ . '/../Fixtures/templates'));

		Assert::same('fallback_path', $resolver->resolve());
	}

	public function testFallbackResolverShouldBeInvokedWithCustomType(): void
	{
		$fallback = Mockery::mock(TemplateFileResolverInterface::class);

		$fallback->shouldReceive('resolve')
			->once()
			->with('prefix')
			->andReturns('prefix.fallback_path');

		$resolver = new ManualTemplateFileResolver($fallback, new Metadata(AComponent::class, 'AComponent', __DIR__ . '/../Fixtures/templates'));

		Assert::same('prefix.fallback_path', $resolver->resolve('prefix'));
	}

	public function testExceptionShouldBeThrownIfMissingDefaultAbsoluteFileIsSet(): void
	{
		$fallback = Mockery::mock(TemplateFileResolverInterface::class);
		$resolver = new ManualTemplateFileResolver($fallback, new Metadata(AComponent::class, 'AComponent', __DIR__ . '/../Fixtures/templates'));

		Assert::exception(
			static fn () => $resolver->setFile(__DIR__ . '/missingFile.latte'),
			RuntimeException::class,
			'Template file %A%/missingFile.latte for component SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\AComponent does not exists.'
		);
	}

	public function testExceptionShouldBeThrownIfMissingPrefixedAbsoluteFileIsSet(): void
	{
		$fallback = Mockery::mock(TemplateFileResolverInterface::class);
		$resolver = new ManualTemplateFileResolver($fallback, new Metadata(AComponent::class, 'AComponent', __DIR__ . '/../Fixtures/templates'));

		Assert::exception(
			static fn () => $resolver->setFile(__DIR__ . '/missingFile.latte', 'prefix'),
			RuntimeException::class,
			'Template file %A%/missingFile.latte for component SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\AComponent does not exists.'
		);
	}

	public function testExceptionShouldBeThrownIfMissingDefaultRelativeFileIsSet(): void
	{
		$fallback = Mockery::mock(TemplateFileResolverInterface::class);
		$resolver = new ManualTemplateFileResolver($fallback, new Metadata(AComponent::class, 'AComponent', __DIR__ . '/../Fixtures/templates'));

		Assert::exception(
			static fn () => $resolver->setRelativeFile('missingFile.latte'),
			RuntimeException::class,
			'Template file %A%/missingFile.latte for component SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\AComponent does not exists.'
		);
	}

	public function testExceptionShouldBeThrownIfMissingPrefixedRelativeFileIsSet(): void
	{
		$fallback = Mockery::mock(TemplateFileResolverInterface::class);
		$resolver = new ManualTemplateFileResolver($fallback, new Metadata(AComponent::class, 'AComponent', __DIR__ . '/../Fixtures/templates'));

		Assert::exception(
			static fn () => $resolver->setRelativeFile('missingFile.latte', 'prefix'),
			RuntimeException::class,
			'Template file %A%/missingFile.latte for component SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\AComponent does not exists.'
		);
	}

	public function testDefaultAbsoluteFileShouldBeSetAndResolved(): void
	{
		$fallback = Mockery::mock(TemplateFileResolverInterface::class);
		$resolver = new ManualTemplateFileResolver($fallback, new Metadata(AComponent::class, 'AComponent', __DIR__ . '/../Fixtures/templates'));

		$resolver->setFile(__DIR__ . '/../Fixtures/templates/AComponent.latte');

		Assert::same(realpath(__DIR__ . '/../Fixtures/templates/AComponent.latte'), $resolver->resolve());
	}

	public function testPrefixedAbsoluteFileShouldBeSetAndResolved(): void
	{
		$fallback = Mockery::mock(TemplateFileResolverInterface::class);
		$resolver = new ManualTemplateFileResolver($fallback, new Metadata(AComponent::class, 'AComponent', __DIR__ . '/../Fixtures/templates'));

		$resolver->setFile(__DIR__ . '/../Fixtures/templates/AComponent.latte');
		$resolver->setFile(__DIR__ . '/../Fixtures/templates/second.AComponent.latte', 'prefix');

		Assert::same(realpath(__DIR__ . '/../Fixtures/templates/second.AComponent.latte'), $resolver->resolve('prefix'));
	}

	public function testDefaultRelativeFileShouldBeSetAndResolved(): void
	{
		$fallback = Mockery::mock(TemplateFileResolverInterface::class);
		$resolver = new ManualTemplateFileResolver($fallback, new Metadata(AComponent::class, 'AComponent', __DIR__ . '/../Fixtures/templates'));

		$resolver->setRelativeFile('AComponent.latte');

		Assert::same(realpath(__DIR__ . '/../Fixtures/templates/AComponent.latte'), $resolver->resolve());
	}

	public function testPrefixedRelativeFileShouldBeSetAndResolved(): void
	{
		$fallback = Mockery::mock(TemplateFileResolverInterface::class);
		$resolver = new ManualTemplateFileResolver($fallback, new Metadata(AComponent::class, 'AComponent', __DIR__ . '/../Fixtures/templates'));

		$resolver->setRelativeFile('AComponent.latte');
		$resolver->setRelativeFile('second.AComponent.latte', 'prefix');

		Assert::same(realpath(__DIR__ . '/../Fixtures/templates/second.AComponent.latte'), $resolver->resolve('prefix'));
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}
}

(new ManualTemplateFileResolverTest())->run();

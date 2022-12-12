<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\TemplateResolver;

use Tester\Assert;
use Tester\TestCase;
use RuntimeException;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\Metadata;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\AComponent;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\BComponent;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\AutomaticTemplateFileResolver;
use function realpath;

require __DIR__ . '/../bootstrap.php';

final class AutomaticTemplateFileResolverTest extends TestCase
{
	public function testExceptionShouldBeThrownWhileResolvingIfDefaultFileIsMissing(): void
	{
		$resolver = new AutomaticTemplateFileResolver(new Metadata(BComponent::class, 'BComponent', __DIR__ . '/../Fixtures/templates'));

		Assert::exception(
			static fn () => $resolver->resolve(),
			RuntimeException::class,
			'Can not find template file for component SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\BComponent [type is default (empty)].'
		);
	}

	public function testExceptionShouldBeThrownWhileResolvingIfPrefixedFileIsMissing(): void
	{
		$resolver = new AutomaticTemplateFileResolver(new Metadata(AComponent::class, 'AComponent', __DIR__ . '/../Fixtures/templates'));

		Assert::exception(
			static fn () => $resolver->resolve('missing'),
			RuntimeException::class,
			'Can not find template file for component SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\AComponent [type \'missing\'].'
		);
	}

	public function testDefaultFileShouldBeResolved(): void
	{
		$resolver = new AutomaticTemplateFileResolver(new Metadata(AComponent::class, 'AComponent', __DIR__ . '/../Fixtures/templates'));

		Assert::same(realpath(__DIR__ . '/../Fixtures/templates/AComponent.latte'), $resolver->resolve());
	}

	public function testPrefixedFileShouldBeResolved(): void
	{
		$resolver = new AutomaticTemplateFileResolver(new Metadata(AComponent::class, 'AComponent', __DIR__ . '/../Fixtures/templates'));

		Assert::same(realpath(__DIR__ . '/../Fixtures/templates/second.AComponent.latte'), $resolver->resolve('second'));
	}
}

(new AutomaticTemplateFileResolverTest())->run();

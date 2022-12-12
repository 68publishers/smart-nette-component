<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\TemplateResolver;

use Closure;
use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures\AComponent;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\ManualTemplateFileResolver;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\TemplateFileResolverFactory;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\AutomaticTemplateFileResolver;
use function assert;
use function realpath;
use function call_user_func;

require __DIR__ . '/../bootstrap.php';

final class TemplateFileResolverFactoryTest extends TestCase
{
	public function testResolverShouldBeCreatedWithRelativePath(): void
	{
		$resolver = TemplateFileResolverFactory::create(AComponent::class, 'templates');

		call_user_func(Closure::bind(
			static function () use ($resolver) {
				$fallback = $resolver->fallback;
				$metadata = $resolver->metadata;

				Assert::type(AutomaticTemplateFileResolver::class, $fallback);
				assert($fallback instanceof AutomaticTemplateFileResolver);

				Assert::same($metadata, $fallback->getMetadata());
				Assert::same(AComponent::class, $metadata->name);
				Assert::same('AComponent', $metadata->shortName);
				Assert::same(realpath(__DIR__ . '/../Fixtures/templates'), $metadata->basePath);
			},
			null,
			ManualTemplateFileResolver::class
		));
	}

	public function testResolverShouldBeCreatedWithAbsolutePath(): void
	{
		$resolver = TemplateFileResolverFactory::create(AComponent::class, __DIR__ . '/../Fixtures/templates2');

		call_user_func(Closure::bind(
			static function () use ($resolver) {
				$fallback = $resolver->fallback;
				$metadata = $resolver->metadata;

				Assert::type(AutomaticTemplateFileResolver::class, $fallback);
				assert($fallback instanceof AutomaticTemplateFileResolver);

				Assert::same($metadata, $fallback->getMetadata());
				Assert::same(AComponent::class, $metadata->name);
				Assert::same('AComponent', $metadata->shortName);
				Assert::same(realpath(__DIR__ . '/../Fixtures/templates2'), $metadata->basePath);
			},
			null,
			ManualTemplateFileResolver::class
		));
	}
}

(new TemplateFileResolverFactoryTest())->run();

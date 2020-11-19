<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Cases\TemplateResolver;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixture\EmptyClass;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\TemplateFileResolverFactory;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\TemplateFileResolverInterface;

require __DIR__ . '/../../bootstrap.php';

final class TemplateFileResolverFactoryTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testResolverCreating(): void
	{
		$resolver = TemplateFileResolverFactory::create(
			EmptyClass::class,
			'templates'
		);

		Assert::type(TemplateFileResolverInterface::class, $resolver);
	}
}

(new TemplateFileResolverFactoryTest())->run();

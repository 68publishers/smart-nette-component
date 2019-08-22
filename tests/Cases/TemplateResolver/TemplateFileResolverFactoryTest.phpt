<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Cases\TemplateResolver;

use Tester;
use SixtyEightPublishers;

require __DIR__ . '/../../bootstrap.php';

final class TemplateFileResolverFactoryTest extends Tester\TestCase
{
	/**
	 * @return void
	 */
	public function testResolverCreating(): void
	{
		$resolver = SixtyEightPublishers\SmartNetteComponent\TemplateResolver\TemplateFileResolverFactory::create(
			SixtyEightPublishers\SmartNetteComponent\Tests\Fixture\EmptyClass::class,
			'templates'
		);

		Tester\Assert::type(SixtyEightPublishers\SmartNetteComponent\TemplateResolver\ITemplateFileResolver::class, $resolver);
	}
}

(new TemplateFileResolverFactoryTest())->run();

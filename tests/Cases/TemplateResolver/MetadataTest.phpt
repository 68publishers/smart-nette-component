<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Cases\TemplateResolver;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\Metadata;

require __DIR__ . '/../../bootstrap.php';

final class MetadataTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testBase(): void
	{
		$metadata = new Metadata(
			'name',
			'short_name',
			'base_path'
		);

		Assert::same('name', $metadata->getName());
		Assert::same('name', $metadata->name);

		Assert::same('short_name', $metadata->getShortName());
		Assert::same('short_name', $metadata->shortName);

		Assert::same('base_path', $metadata->getBasePath());
		Assert::same('base_path', $metadata->basePath);
	}
}

(new MetadataTest())->run();

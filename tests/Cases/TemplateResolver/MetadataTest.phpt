<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Cases;

use Tester;
use SixtyEightPublishers;

require __DIR__ . '/../../bootstrap.php';

final class MetadataTest extends Tester\TestCase
{
	/**
	 * @return void
	 */
	public function testBase(): void
	{
		$metadata = new SixtyEightPublishers\SmartNetteComponent\TemplateResolver\Metadata(
			'name',
			'short_name',
			'base_path'
		);

		Tester\Assert::same('name', $metadata->getName());
		Tester\Assert::same('name', $metadata->name);

		Tester\Assert::same('short_name', $metadata->getShortName());
		Tester\Assert::same('short_name', $metadata->shortName);

		Tester\Assert::same('base_path', $metadata->getBasePath());
		Tester\Assert::same('base_path', $metadata->basePath);
	}
}

(new MetadataTest())->run();

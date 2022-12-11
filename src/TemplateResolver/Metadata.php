<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\TemplateResolver;

final class Metadata
{
	public function __construct(
		public readonly string $name,
		public readonly string $shortName,
		public readonly string $basePath
	) {
	}
}

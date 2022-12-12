<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\TemplateResolver;

final class Metadata
{
	public readonly string $basePath;

	public function __construct(
		public readonly string $name,
		public readonly string $shortName,
		string $basePath
	) {
		$this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
	}
}

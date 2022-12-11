<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\TemplateResolver;

interface TemplateFileResolverInterface
{
	public function resolve(string $type = ''): string;
}

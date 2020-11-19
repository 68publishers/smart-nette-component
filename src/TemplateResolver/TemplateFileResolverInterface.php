<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\TemplateResolver;

interface TemplateFileResolverInterface
{
	/**
	 * @param string $type
	 *
	 * @return string
	 */
	public function resolve(string $type = ''): string;
}

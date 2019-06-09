<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\TemplateResolver;

interface ITemplateFileResolver
{
	/**
	 * @param string $type
	 *
	 * @return string
	 */
	public function resolve(string $type = ''): string;
}

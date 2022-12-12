<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Attribute;

final class AttributePrototype implements AttributeInterface
{
	/**
	 * @param class-string $classname
	 * @param array<mixed> $arguments
	 */
	public function __construct(
		public readonly string $classname,
		public readonly array $arguments,
	) {
	}
}

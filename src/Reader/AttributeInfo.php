<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Reader;

use SixtyEightPublishers\SmartNetteComponent\Attribute\AttributeInterface;

final class AttributeInfo
{
	/**
	 * @param class-string $classname
	 */
	public function __construct(
		public readonly string $classname,
		public readonly AttributeInterface $attribute,
	) {
	}
}

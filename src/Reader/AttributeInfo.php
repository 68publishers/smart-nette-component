<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Reader;

use SixtyEightPublishers\SmartNetteComponent\Attribute\AttributeInterface;

final class AttributeInfo
{
	public function __construct(
		public readonly string $classname,
		public readonly AttributeInterface $attribute,
	) {
	}
}

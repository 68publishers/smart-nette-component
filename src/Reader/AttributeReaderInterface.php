<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Reader;

interface AttributeReaderInterface
{
	/**
	 * @return array<AttributeInfo>
	 */
	public function getClassAttributes(string $classname, ?string $stopBeforeParent = null): array;

	/**
	 * @return array<AttributeInfo>
	 */
	public function getMethodAttributes(string $classname, string $method, ?string $stopBeforeParent = null): array;
}

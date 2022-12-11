<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Reader;

use SixtyEightPublishers\SmartNetteComponent\Attribute\AttributeInterface;
use function array_map;
use function array_merge;
use function array_reverse;
use function array_key_exists;

final class StaticAttributeReader implements AttributeReaderInterface
{
	public function __construct(
		private readonly AttributesMap $map,
	) {
	}

	public function getClassAttributes(string $classname, ?string $stopBeforeParent = null): array
	{
		return $this->doFindAttributes($classname, null, $stopBeforeParent);
	}

	public function getMethodAttributes(string $classname, string $method, ?string $stopBeforeParent = null): array
	{
		return $this->doFindAttributes($classname, $method, $stopBeforeParent);
	}

	public function doFindAttributes(string $classname, ?string $method, ?string $stopBeforeParent): array
	{
		if (!array_key_exists($classname, $this->map->classHierarchy)) {
			return [];
		}

		$current = $classname;
		$attributes = [];

		do {
			$key = $current . '::' . ($method ?? 'class');

			if (isset($this->map->attributes[$key])) {
				$attributes[] = array_map(
					static fn (AttributeInterface $attribute): AttributeInfo => new AttributeInfo($current, $attribute),
					$this->map->attributes[$key]
				);
			}

			if ($current === $stopBeforeParent) {
				$current = false;
			}

			if ($current) {
				$current = $this->map->classHierarchy[$current];

				if ($current === $stopBeforeParent) {
					$current = false;
				}
			}
		} while ($current);

		return array_merge(...array_reverse($attributes));
	}
}

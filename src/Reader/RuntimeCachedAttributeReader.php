<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Reader;

final class RuntimeCachedAttributeReader implements AttributeReaderInterface
{
	/** @var array<string, array<AttributeInfo>> */
	private array $cache = [];

	public function __construct(
		private readonly AttributeReaderInterface $inner
	) {
	}

	public function getClassAttributes(string $classname, ?string $stopBeforeParent = null): array
	{
		$key = $classname . '.' . ($stopBeforeParent ?? 'null');

		return $this->cache[$key] ?? $this->cache[$key] = $this->inner->getClassAttributes($classname, $stopBeforeParent);
	}

	public function getMethodAttributes(string $classname, string $method, ?string $stopBeforeParent = null): array
	{
		$key = $classname . '.' . $method . '.' . ($stopBeforeParent ?? 'null');

		return $this->cache[$key] ?? $this->cache[$key] = $this->inner->getMethodAttributes($classname, $method, $stopBeforeParent);
	}
}

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Reader;

use ReflectionClass;
use ReflectionAttribute;
use ReflectionException;
use SixtyEightPublishers\SmartNetteComponent\Attribute\AttributeInterface;
use SixtyEightPublishers\SmartNetteComponent\Attribute\AttributePrototype;
use function array_map;
use function array_merge;
use function array_reverse;

final class AttributeReader implements AttributeReaderInterface
{
	private bool $useAttributePrototypes = false;

	public static function createWithAttributePrototypes(): self
	{
		$reader = new self();
		$reader->useAttributePrototypes = true;

		return $reader;
	}

	/**
	 * @throws ReflectionException
	 */
	public function getClassAttributes(string $classname, ?string $stopBeforeParent = null): array
	{
		return $this->doFindAttributes($classname, null, $stopBeforeParent);
	}

	/**
	 * @throws ReflectionException
	 */
	public function getMethodAttributes(string $classname, string $method, ?string $stopBeforeParent = null): array
	{
		return $this->doFindAttributes($classname, $method, $stopBeforeParent);
	}

	/**
	 * @param class-string $classname
	 *
	 * @return array<AttributeInfo>
	 *
	 * @throws ReflectionException
	 */
	private function doFindAttributes(string $classname, ?string $method, ?string $stopBeforeParent): array
	{
		$classReflection = new ReflectionClass($classname);
		$attributes = [];

		do {
			if (null !== $method) {
				$reflector = $classReflection->hasMethod($method) ? $classReflection->getMethod($method) : false;
				$reflector = $reflector && $reflector->getDeclaringClass()->getName() === $classReflection->getName() ? $reflector : false;
			} else {
				$reflector = $classReflection;
			}

			$attributes[] = array_map(
				fn (ReflectionAttribute $attribute): AttributeInfo =>new AttributeInfo(
					$classReflection->getName(),
					$this->useAttributePrototypes ? new AttributePrototype($attribute->getName(), $attribute->getArguments()) : $attribute->newInstance()
				),
				$reflector ? $reflector->getAttributes(AttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF) : []
			);

			if ($classReflection->getName() === $stopBeforeParent) {
				$classReflection = false;
			}

			if ($classReflection) {
				$classReflection = $classReflection->getParentClass();

				if ($classReflection && $classReflection->getName() === $stopBeforeParent) {
					$classReflection = false;
				}
			}
		} while ($classReflection);

		return array_merge(...array_reverse($attributes));
	}
}

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Reader;

use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use Nette\Application\IPresenter;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use SixtyEightPublishers\SmartNetteComponent\Attribute\AttributeInterface;
use function count;
use function assert;
use function array_merge;
use function array_filter;
use function array_values;
use function str_starts_with;
use function array_key_exists;

final class AttributesMap
{
	/**
	 * @param array<string, string|false>              $classHierarchy The key is classname, the value is the parent classname or false
	 * @param array<string, array<AttributeInterface>> $attributes     The key is class, the value is an array of attributes
	 */
	public function __construct(
		public readonly array $classHierarchy,
		public readonly array $attributes,
	) {
	}

	/**
	 * @throws ReflectionException
	 */
	public static function createFromClassList(array $classList, bool $useAttributePrototypes = false): self
	{
		$map = new self([], []);
		$reader = $useAttributePrototypes ? AttributeReader::createWithAttributePrototypes() : new AttributeReader();

		foreach ($classList as $classname) {
			$reflection = new ReflectionClass($classname);

			if (!($reflection->isSubclassOf(Control::class) || $reflection->isSubclassOf(IPresenter::class)) || array_key_exists($reflection->getName(), $map->classHierarchy)) {
				continue;
			}

			do {
				$parent = $reflection->getParentClass();
				$parent = !$parent || Presenter::class === $parent->getName() || Control::class === $parent->getName() ? false : $parent;

				$methods = [];
				$classAttributes = $reflection->isSubclassOf(IPresenter::class)
					? $reader->getClassAttributes($reflection->getName(), $reflection->getName())
					: [];

				if ($reflection->isSubclassOf(IPresenter::class)) {
					$methods = array_values(
						array_filter(
							$reflection->getMethods(ReflectionMethod::IS_PUBLIC),
							static fn (ReflectionMethod $method): bool => str_starts_with($method->getName(), 'action') && $method->getDeclaringClass()->getName() === $reflection->getName()
						)
					);
				}

				$methods = array_merge(
					$methods,
					array_values(
						array_filter(
							$reflection->getMethods(ReflectionMethod::IS_PUBLIC),
							static fn (ReflectionMethod $method): bool => str_starts_with($method->getName(), 'handle') && $method->getDeclaringClass()->getName() === $reflection->getName()
						)
					)
				);

				$map = $map->withClass($reflection->getName(), $parent ? $parent->getName() : false);

				if (0 < count($classAttributes)) {
					$map = $map->withClassAttributes(
						$reflection->getName(),
						array_map(static fn (AttributeInfo $info): AttributeInterface => $info->attribute, $classAttributes)
					);
				}

				foreach ($methods as $method) {
					assert($method instanceof ReflectionMethod);
					$attributes = $reader->getMethodAttributes($reflection->getName(), $method->getName(), $reflection->getName());

					if (0 < count($attributes)) {
						$map = $map->withMethodAttributes(
							$reflection->getName(),
							$method->getName(),
							array_map(static fn (AttributeInfo $info): AttributeInterface => $info->attribute, $attributes)
						);
					}
				}

				$reflection = $parent;

				if ($reflection && array_key_exists($reflection->getName(), $map->classHierarchy)) {
					$reflection = false;
				}
			} while ($reflection);
		}

		return $map;
	}

	public function withClass(string $classname, string|false $parentClassname): self
	{
		$classHierarchy = $this->classHierarchy;
		$classHierarchy[$classname] = $parentClassname;

		return new self($classHierarchy, $this->attributes);
	}

	/**
	 * @param array<AttributeInterface> $attributes
	 */
	public function withClassAttributes(string $classname, array $attributes): self
	{
		$allAttributes = $this->attributes;
		$allAttributes[$classname . '::class'] = $attributes;

		return new self($this->classHierarchy, $allAttributes);
	}

	/**
	 * @param array<AttributeInterface> $attributes
	 */
	public function withMethodAttributes(string $classname, string $method, array $attributes): self
	{
		$allAttributes = $this->attributes;
		$allAttributes[$classname . '::' . $method] = $attributes;

		return new self($this->classHierarchy, $allAttributes);
	}
}

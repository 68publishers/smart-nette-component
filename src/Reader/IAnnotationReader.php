<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Reader;

interface IAnnotationReader
{
	/**
	 * @param \ReflectionClass $reflectionClass
	 * @param NULL|string      $stopBeforeParent
	 *
	 * @return \SixtyEightPublishers\SmartNetteComponent\Reader\ClassAnnotation[]
	 */
	public function getClassAnnotations(\ReflectionClass $reflectionClass, ?string $stopBeforeParent = NULL): array;

	/**
	 * @param \ReflectionMethod $reflectionMethod
	 *
	 * @return object[] Array of annotation objects
	 */
	public function getMethodAnnotations(\ReflectionMethod $reflectionMethod): array;
}

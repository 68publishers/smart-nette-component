<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Reader;

use ReflectionClass;
use ReflectionMethod;
use Nette\SmartObject;
use Doctrine\Common\Annotations\Reader;

final class DoctrineAnnotationReader implements AnnotationReaderInterface
{
	use SmartObject;

	/** @var \Doctrine\Common\Annotations\Reader  */
	private $reader;

	/** @var array  */
	private $classes = [];

	/**
	 * @param \Doctrine\Common\Annotations\Reader $reader
	 */
	public function __construct(Reader $reader)
	{
		$this->reader = $reader;
	}

	/**
	 * @param \ReflectionClass $reflectionClass
	 * @param NULL|string      $stopBeforeParent
	 *
	 * @return \SixtyEightPublishers\SmartNetteComponent\Reader\ClassAnnotation[]
	 */
	private function composeClassAnnotations(ReflectionClass $reflectionClass, ?string $stopBeforeParent = NULL): array
	{
		$parent = $reflectionClass->getParentClass();

		$result = array_values(array_map(static function ($annotation) use ($reflectionClass) {
			return new ClassAnnotation($reflectionClass, $annotation);
		}, $this->reader->getClassAnnotations($reflectionClass)));

		if ($parent instanceof ReflectionClass && (NULL === $stopBeforeParent || ($reflectionClass->getName() !== $stopBeforeParent && $parent->getName() !== $stopBeforeParent))) {
			$result = array_merge($this->composeClassAnnotations($parent, $stopBeforeParent), $result);
		}

		return $result;
	}

	/********************* interface \SixtyEightPublishers\SmartNetteComponent\Reader\IAnnotationReader *********************/

	/**
	 * {@inheritdoc}
	 */
	public function getClassAnnotations(ReflectionClass $reflectionClass, ?string $stopBeforeParent = NULL): array
	{
		$key = sprintf('%s.%s', $reflectionClass->getName(), (string) $stopBeforeParent);

		if (!array_key_exists($key, $this->classes)) {
			$this->classes[$key] = $this->composeClassAnnotations($reflectionClass, $stopBeforeParent);
		}

		return $this->classes[$key];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMethodAnnotations(ReflectionMethod $reflectionMethod): array
	{
		return $this->reader->getMethodAnnotations($reflectionMethod);
	}
}

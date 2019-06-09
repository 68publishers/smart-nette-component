<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Reader;

use Nette;

/**
 * @property-read \ReflectionClass $reflectionClass
 * @property-read object $annotation
 */
class ClassAnnotation
{
	use Nette\SmartObject;

	/** @var \ReflectionClass  */
	private $reflectionClass;

	/** @var object  */
	private $annotation;

	/**
	 * @param \ReflectionClass $reflectionClass
	 * @param object           $annotation
	 */
	public function __construct(\ReflectionClass $reflectionClass, $annotation)
	{
		$this->reflectionClass = $reflectionClass;
		$this->annotation = $annotation;
	}

	/**
	 * @return \ReflectionClass
	 */
	public function getReflectionClass(): \ReflectionClass
	{
		return $this->reflectionClass;
	}

	/**
	 * @return object
	 */
	public function getAnnotation()
	{
		return $this->annotation;
	}
}

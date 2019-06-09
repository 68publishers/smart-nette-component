<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\UI;

use SixtyEightPublishers;

/**
 * @internal
 */
trait TAnnotatedControl
{
	/** @var NULL|\SixtyEightPublishers\SmartNetteComponent\Reader\IAnnotationReader */
	private $annotationReader;

	/**
	 * Override this method is you want to redirect, show flash message etc.
	 *
	 * @param \SixtyEightPublishers\SmartNetteComponent\Annotation\IAuthorizationAnnotation $annotation
	 *
	 * @return void
	 */
	protected function onForbiddenRequest(SixtyEightPublishers\SmartNetteComponent\Annotation\IAuthorizationAnnotation $annotation): void
	{
	}

	/**
	 * @internal
	 *
	 * @param \SixtyEightPublishers\SmartNetteComponent\Reader\IAnnotationReader $annotationReader
	 *
	 * @return void
	 */
	public function injectAnnotationReader(SixtyEightPublishers\SmartNetteComponent\Reader\IAnnotationReader $annotationReader): void
	{
		$this->annotationReader = $annotationReader;
	}

	/**
	 * @param \ReflectionMethod $reflectionMethod
	 *
	 * @return void
	 * @throws \SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException
	 */
	private function checkAuthorizationOnMethod(\ReflectionMethod $reflectionMethod): void
	{
		foreach ($this->annotationReader->getMethodAnnotations($reflectionMethod) as $methodAnnotation) {
			if ($methodAnnotation instanceof SixtyEightPublishers\SmartNetteComponent\Annotation\IAuthorizationAnnotation) {
				$this->checkAuthorizationAnnotation($methodAnnotation);
			}
		}
	}

	/**
	 * @param \SixtyEightPublishers\SmartNetteComponent\Annotation\IAuthorizationAnnotation $annotation
	 *
	 * @return void
	 * @throws \SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException
	 */
	private function checkAuthorizationAnnotation(SixtyEightPublishers\SmartNetteComponent\Annotation\IAuthorizationAnnotation $annotation): void
	{
		if (FALSE === $annotation->isAllowed($this->user)) {
			$this->onForbiddenRequest($annotation); # redirects here

			throw new SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException($annotation);
		}
	}
}

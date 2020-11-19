<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\UI;

use SixtyEightPublishers\SmartNetteComponent\Reader\AnnotationReaderInterface;
use SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException;
use SixtyEightPublishers\SmartNetteComponent\Annotation\AuthorizationAnnotationInterface;

/**
 * @internal
 */
trait AnnotatedControlTrait
{
	/** @var NULL|\SixtyEightPublishers\SmartNetteComponent\Reader\AnnotationReaderInterface */
	private $annotationReader;

	/**
	 * Override this method is you want to redirect, show flash message etc.
	 *
	 * @param \SixtyEightPublishers\SmartNetteComponent\Annotation\AuthorizationAnnotationInterface $annotation
	 *
	 * @return void
	 */
	protected function onForbiddenRequest(AuthorizationAnnotationInterface $annotation): void
	{
	}

	/**
	 * @internal
	 *
	 * @param \SixtyEightPublishers\SmartNetteComponent\Reader\AnnotationReaderInterface $annotationReader
	 *
	 * @return void
	 */
	public function injectAnnotationReader(AnnotationReaderInterface $annotationReader): void
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
			if ($methodAnnotation instanceof AuthorizationAnnotationInterface) {
				$this->checkAuthorizationAnnotation($methodAnnotation);
			}
		}
	}

	/**
	 * @param \SixtyEightPublishers\SmartNetteComponent\Annotation\AuthorizationAnnotationInterface $annotation
	 *
	 * @return void
	 * @throws \SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException
	 */
	private function checkAuthorizationAnnotation(AuthorizationAnnotationInterface $annotation): void
	{
		if (FALSE === $annotation->isAllowed($this->user)) {
			$this->onForbiddenRequest($annotation); # redirects here

			throw new ForbiddenRequestException($annotation);
		}
	}
}

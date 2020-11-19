<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\UI;

use Nette\Application\UI\Presenter as NettePresenter;
use SixtyEightPublishers\SmartNetteComponent\Annotation\Layout;
use SixtyEightPublishers\SmartNetteComponent\Reader\ClassAnnotation;
use SixtyEightPublishers\SmartNetteComponent\Annotation\AuthorizationAnnotationInterface;

abstract class Presenter extends NettePresenter
{
	use AnnotatedControlTrait;

	/** @var string[] */
	private $customLayouts = [];

	/**
	 * {@inheritdoc}
	 * @throws \Nette\Application\ForbiddenRequestException
	 */
	public function checkRequirements($element): void
	{
		parent::checkRequirements($element);

		# Process Class annotations. Called in Presenter::run()
		if ($element instanceof \ReflectionClass) {
			foreach ($this->annotationReader->getClassAnnotations($element, self::class) as $classAnnotation) {
				$this->processClassAnnotation($classAnnotation);
			}
		}

		# Process Action or Signal (handle) method's annotations. Called in Component::tryCall()
		if ($element instanceof \ReflectionMethod) {
			$this->checkAuthorizationOnMethod($element);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function formatLayoutTemplateFiles(): array
	{
		return array_merge($this->customLayouts, parent::formatLayoutTemplateFiles());
	}

	/**
	 * Override if you wanna process own annotations
	 *
	 * @param \SixtyEightPublishers\SmartNetteComponent\Reader\ClassAnnotation $classAnnotation
	 *
	 * @throws \SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException
	 */
	protected function processClassAnnotation(ClassAnnotation $classAnnotation): void
	{
		$annotation = $classAnnotation->getAnnotation();

		if ($annotation instanceof AuthorizationAnnotationInterface) {
			$this->checkAuthorizationAnnotation($annotation);
		}

		if ($annotation instanceof Layout) {
			$this->customLayouts[] = dirname($classAnnotation->getReflectionClass()->getFileName()) . '/' . $annotation->path;
		}
	}
}

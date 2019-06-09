<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\UI;

use Nette;
use SixtyEightPublishers\SmartNetteComponent;

abstract class Presenter extends Nette\Application\UI\Presenter
{
	use TAnnotatedControl;

	/** @var string[] */
	private $customLayouts = [];

	/**
	 * {@inheritdoc}
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
	public function formatLayoutTemplateFiles()
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
	protected function processClassAnnotation(SmartNetteComponent\Reader\ClassAnnotation $classAnnotation): void
	{
		$annotation = $classAnnotation->getAnnotation();

		if ($annotation instanceof SmartNetteComponent\Annotation\IAuthorizationAnnotation) {
			$this->checkAuthorizationAnnotation($annotation);
		}

		if ($annotation instanceof SmartNetteComponent\Annotation\Layout) {
			$this->customLayouts[] = dirname($classAnnotation->getReflectionClass()->getFileName()) . '/' . $annotation->path;
		}
	}
}

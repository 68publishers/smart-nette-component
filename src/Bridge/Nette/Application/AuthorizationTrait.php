<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\Application;

use ReflectionClass;
use ReflectionMethod;
use SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizatorAwareTrait;
use function str_starts_with;

/**
 * For Presenters and Controls
 */
trait AuthorizationTrait
{
	use ComponentAuthorizatorAwareTrait;

	/**
	 * @throws \SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException
	 */
	public function checkRequirements($element): void
	{
		parent::checkRequirements($element);

		try {
			# Process class authorization. Called in Presenter::run()
			if ($element instanceof ReflectionClass) {
				$this->componentAuthorizator->checkPresenter($element->getName());

				return;
			}

			# Process action authorization. Called in Component::tryCall()
			if ($element instanceof ReflectionMethod && str_starts_with($element->getName(), 'action')) {
				$this->componentAuthorizator->checkAction($element->getDeclaringClass()->getName(), $element->getName());

				return;
			}

			# Process signal authorization. Called in Component::tryCall()
			if ($element instanceof ReflectionMethod && str_starts_with($element->getName(), 'handle')) {
				$this->componentAuthorizator->checkSignal($element->getDeclaringClass()->getName(), $element->getName());
			}
		} catch (ForbiddenRequestException $e) {
			$this->onForbiddenRequest($e);

			throw $e;
		}
	}

	/**
	 * Override this method is you want to redirect, show flash message etc.
	 */
	protected function onForbiddenRequest(ForbiddenRequestException $exception): void
	{
	}
}

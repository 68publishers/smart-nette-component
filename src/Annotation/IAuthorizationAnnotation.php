<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Annotation;

use Nette;

interface IAuthorizationAnnotation
{
	/**
	 * @param \Nette\Security\User $user
	 *
	 * @return bool
	 */
	public function isAllowed(Nette\Security\User $user): bool;
}

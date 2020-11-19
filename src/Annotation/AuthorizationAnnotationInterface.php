<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Annotation;

use Nette\Security\User;

interface AuthorizationAnnotationInterface
{
	/**
	 * @param \Nette\Security\User $user
	 *
	 * @return bool
	 */
	public function isAllowed(User $user): bool;
}

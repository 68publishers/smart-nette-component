<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Annotation;

use Nette\Security\User;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
final class LoggedOut implements AuthorizationAnnotationInterface
{
	/******************** interface \SixtyEightPublishers\SmartNetteComponent\Annotation\IAuthorizationAnnotation ********************/

	/**
	 * {@inheritdoc}
	 */
	public function isAllowed(User $user): bool
	{
		return FALSE === $user->isLoggedIn();
	}
}

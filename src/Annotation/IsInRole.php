<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Annotation;

use Nette;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
final class IsInRole implements IAuthorizationAnnotation
{
	/** @var string  */
	public $name;

	/******************** interface \SixtyEightPublishers\SmartNetteComponent\Annotation\IAuthorizationAnnotation ********************/

	/**
	 * {@inheritdoc}
	 */
	public function isAllowed(Nette\Security\User $user): bool
	{
		return $user->isInRole($this->name);
	}
}

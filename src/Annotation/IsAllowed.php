<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Annotation;

use Nette;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
final class IsAllowed implements IAuthorizationAnnotation
{
	/** @var string  */
	public $resource = Nette\Security\IAuthorizator::ALL;

	/** @var string  */
	public $privilege = Nette\Security\IAuthorizator::ALL;

	/******************** interface \SixtyEightPublishers\SmartNetteComponent\Annotation\IAuthorizationAnnotation ********************/

	/**
	 * {@inheritdoc}
	 */
	public function isAllowed(Nette\Security\User $user): bool
	{
		return $user->isAllowed($this->resource, $this->privilege);
	}
}

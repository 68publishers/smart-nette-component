<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Annotation;

use Nette\Security\User;
use Nette\Security\IAuthorizator;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
final class IsAllowed implements AuthorizationAnnotationInterface
{
	/** @var string  */
	public $resource = IAuthorizator::ALL;

	/** @var string  */
	public $privilege = IAuthorizator::ALL;

	/******************** interface \SixtyEightPublishers\SmartNetteComponent\Annotation\IAuthorizationAnnotation ********************/

	/**
	 * {@inheritdoc}
	 */
	public function isAllowed(User $user): bool
	{
		return $user->isAllowed($this->resource, $this->privilege);
	}
}

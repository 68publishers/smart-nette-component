<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Authorization;

trait ComponentAuthorizatorAwareTrait
{
	protected ComponentAuthorizatorInterface $componentAuthorizator;

	public function setComponentAuthorizator(ComponentAuthorizatorInterface $componentAuthorizator): void
	{
		$this->componentAuthorizator = $componentAuthorizator;
	}
}

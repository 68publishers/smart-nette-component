<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Authorization;

interface ComponentAuthorizatorAwareInterface
{
	public function setComponentAuthorizator(ComponentAuthorizatorInterface $componentAuthorizator): void;
}

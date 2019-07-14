<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Link;

interface ILinkAuthorizator
{
	/**
	 * @param string $presenterClassName
	 * @param string $action
	 *
	 * @return bool
	 */
	public function isActionAllowed(string $presenterClassName, string $action = 'default'): bool;

	/**
	 * @param string $controlClassName
	 * @param string $signal
	 *
	 * @return bool
	 */
	public function isSignalAllowed(string $controlClassName, string $signal): bool;
}

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\DI;

final class ComponentAuthorizationConfig
{
	public bool $cache;

	/** @var array<string>|false  */
	public array|false $scanDirs;

	public bool $scanComposer;

	/** @var array<string> */
	public array $scanFilters;
}

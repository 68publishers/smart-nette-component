<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures;

interface BComponentFactoryInterface
{
	public function create(): BComponent;
}

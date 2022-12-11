<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Reader;

use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleHandlerInterface;

final class CustomRuleHandler implements RuleHandlerInterface
{
	public function canHandle(RuleInterface $rule): bool
	{
		return false;
	}

	public function __invoke(RuleInterface $rule): void
	{
	}
}

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Authorization;

interface RuleHandlerInterface
{
	public function canHandle(RuleInterface $rule): bool;

	/**
	 * @throws \SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException
	 */
	public function __invoke(RuleInterface $rule): void;
}

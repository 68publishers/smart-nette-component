<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Authorization\Handler;

use Nette\Security\User;
use SixtyEightPublishers\SmartNetteComponent\Attribute\InRole;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleHandlerInterface;
use SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException;
use function assert;

final class InRoleHandler implements RuleHandlerInterface
{
	public function __construct(
		private readonly User $user
	) {
	}

	public function canHandle(RuleInterface $rule): bool
	{
		return $rule instanceof InRole;
	}

	public function __invoke(RuleInterface $rule): void
	{
		assert($rule instanceof InRole);

		if (!$this->user->isInRole($rule->name)) {
			throw new ForbiddenRequestException($rule);
		}
	}
}

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Authorization\Handler;

use Nette\Security\User;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleHandlerInterface;
use SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException;
use function assert;

final class AllowedHandler implements RuleHandlerInterface
{
	public function __construct(
		private readonly User $user
	) {
	}

	public function canHandle(RuleInterface $rule): bool
	{
		return $rule instanceof Allowed;
	}

	public function __invoke(RuleInterface $rule): void
	{
		assert($rule instanceof Allowed);

		if (!$this->user->isAllowed($rule->resource, $rule->privilege)) {
			throw new ForbiddenRequestException($rule);
		}
	}
}

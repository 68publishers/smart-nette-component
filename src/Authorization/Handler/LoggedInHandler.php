<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Authorization\Handler;

use Nette\Security\User;
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedIn;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleHandlerInterface;
use SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException;
use function assert;

final class LoggedInHandler implements RuleHandlerInterface
{
	public function __construct(
		private readonly User $user
	) {
	}

	public function canHandle(RuleInterface $rule): bool
	{
		return $rule instanceof LoggedIn;
	}

	public function __invoke(RuleInterface $rule): void
	{
		assert($rule instanceof LoggedIn);

		if (!$this->user->isLoggedIn()) {
			throw new ForbiddenRequestException($rule);
		}
	}
}

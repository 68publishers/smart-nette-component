<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Authorization\Handler;

use InvalidArgumentException;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleHandlerInterface;
use function sprintf;
use function get_class;

final class HandlerRegistry implements RuleHandlerInterface
{
	/**
	 * @param array<RuleHandlerInterface> $handlers
	 */
	public function __construct(
		private readonly array $handlers
	) {
	}

	public function canHandle(RuleInterface $rule): bool
	{
		return true;
	}

	public function __invoke(RuleInterface $rule): void
	{
		foreach ($this->handlers as $handler) {
			if ($handler->canHandle($rule)) {
				$handler($rule);

				return;
			}
		}

		throw new InvalidArgumentException(sprintf(
			'Can\'t handle rule of type %s.',
			get_class($rule)
		));
	}
}

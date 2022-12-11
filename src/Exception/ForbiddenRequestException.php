<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Exception;

use Throwable;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleInterface;
use Nette\Application\ForbiddenRequestException as NetteForbiddenRequestException;

final class ForbiddenRequestException extends NetteForbiddenRequestException
{
	public function __construct(
		public readonly RuleInterface $rule,
		string $message = '',
		int $httpCode = 0,
		Throwable $previous = null
	) {
		parent::__construct($message, $httpCode ?: $this->code, $previous);
	}
}

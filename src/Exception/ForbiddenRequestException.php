<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Exception;

use Nette;
use SixtyEightPublishers;

final class ForbiddenRequestException extends Nette\Application\ForbiddenRequestException implements IException
{
	/** @var \SixtyEightPublishers\SmartNetteComponent\Annotation\IAuthorizationAnnotation  */
	private $annotation;

	/**
	 * @param \SixtyEightPublishers\SmartNetteComponent\Annotation\IAuthorizationAnnotation $annotation
	 * @param string                                                                        $message
	 * @param int                                                                           $httpCode
	 * @param \Exception|NULL                                                               $previous
	 */
	public function __construct(SixtyEightPublishers\SmartNetteComponent\Annotation\IAuthorizationAnnotation $annotation, string $message = '', int $httpCode = 0, \Exception $previous = NULL)
	{
		parent::__construct($message, $httpCode ?: $this->code, $previous);

		$this->annotation = $annotation;
	}

	/**
	 * @return \SixtyEightPublishers\SmartNetteComponent\Annotation\IAuthorizationAnnotation
	 */
	public function getAnnotation(): SixtyEightPublishers\SmartNetteComponent\Annotation\IAuthorizationAnnotation
	{
		return $this->annotation;
	}
}

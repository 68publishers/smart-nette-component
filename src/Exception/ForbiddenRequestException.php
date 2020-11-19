<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Exception;

use Throwable;
use Nette\Application\ForbiddenRequestException as NetteForbiddenRequestException;
use SixtyEightPublishers\SmartNetteComponent\Annotation\AuthorizationAnnotationInterface;

final class ForbiddenRequestException extends NetteForbiddenRequestException implements ExceptionInterface
{
	/** @var \SixtyEightPublishers\SmartNetteComponent\Annotation\AuthorizationAnnotationInterface  */
	private $annotation;

	/**
	 * @param \SixtyEightPublishers\SmartNetteComponent\Annotation\AuthorizationAnnotationInterface $annotation
	 * @param string                                                                                $message
	 * @param int                                                                                   $httpCode
	 * @param \Throwable|NULL                                                                       $previous
	 */
	public function __construct(AuthorizationAnnotationInterface $annotation, string $message = '', int $httpCode = 0, Throwable $previous = NULL)
	{
		parent::__construct($message, $httpCode ?: $this->code, $previous);

		$this->annotation = $annotation;
	}

	/**
	 * @return \SixtyEightPublishers\SmartNetteComponent\Annotation\AuthorizationAnnotationInterface
	 */
	public function getAnnotation(): AuthorizationAnnotationInterface
	{
		return $this->annotation;
	}
}

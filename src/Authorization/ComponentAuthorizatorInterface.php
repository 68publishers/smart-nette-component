<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Authorization;

interface ComponentAuthorizatorInterface
{
	/**
	 * @throws \SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException
	 */
	public function checkPresenter(string $presenterClassname): void;

	/**
	 * @throws \SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException
	 */
	public function checkAction(string $presenterClassname, string $action): void;

	/**
	 * @throws \SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException
	 */
	public function checkSignal(string $componentClassname, string $signal): void;
}

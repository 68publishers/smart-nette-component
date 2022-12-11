<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Authorization;

interface AuthorizationRulesProviderInterface
{
	/**
	 * @return array<RuleInterface>
	 */
	public function provideForPresenter(string $presenterClassname): array;

	/**
	 * @return array<RuleInterface>
	 */
	public function provideForAction(string $presenterClassname, string $action): array;

	/**
	 * @return array<RuleInterface>
	 */
	public function provideForSignal(string $componentClassname, string $signal): array;
}

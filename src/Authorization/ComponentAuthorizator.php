<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Authorization;

final class ComponentAuthorizator implements ComponentAuthorizatorInterface
{
	public function __construct(
		private readonly AuthorizationRulesProviderInterface $authorizationRulesProvider,
		private readonly RuleHandlerInterface $handler,
	) {
	}

	public function checkPresenter(string $presenterClassname): void
	{
		$this->handleRules(
			$this->authorizationRulesProvider->provideForPresenter($presenterClassname)
		);
	}

	public function checkAction(string $presenterClassname, string $action): void
	{
		$this->handleRules(
			$this->authorizationRulesProvider->provideForAction($presenterClassname, $action)
		);
	}

	public function checkSignal(string $componentClassname, string $signal): void
	{
		$this->handleRules(
			$this->authorizationRulesProvider->provideForSignal($componentClassname, $signal)
		);
	}

	/**
	 * @param array<RuleInterface> $rules
	 *
	 * @throws \SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException
	 */
	private function handleRules(array $rules): void
	{
		foreach ($rules as $rule) {
			($this->handler)($rule);
		}
	}
}

<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Authorization;

use InvalidArgumentException;
use Nette\Application\IPresenter;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use SixtyEightPublishers\SmartNetteComponent\Reader\AttributeInfo;
use SixtyEightPublishers\SmartNetteComponent\Attribute\AttributeInterface;
use SixtyEightPublishers\SmartNetteComponent\Reader\AttributeReaderInterface;
use function sprintf;
use function ucfirst;
use function array_map;
use function array_filter;
use function array_values;
use function is_subclass_of;

final class AttributeBasedAuthorizationRulesProvider implements AuthorizationRulesProviderInterface
{
	public function __construct(
		private readonly AttributeReaderInterface $reader,
	) {
	}

	public function provideForPresenter(string $presenterClassname): array
	{
		if (!is_subclass_of($presenterClassname, IPresenter::class, true)) {
			throw new InvalidArgumentException(sprintf(
				'Class %s is not a Presenter.',
				$presenterClassname
			));
		}

		return $this->filterRules(
			$this->reader->getClassAttributes($presenterClassname, Presenter::class)
		);
	}

	public function provideForAction(string $presenterClassname, string $action): array
	{
		if (!is_subclass_of($presenterClassname, IPresenter::class, true)) {
			throw new InvalidArgumentException(sprintf(
				'Class %s is not a Presenter.',
				$presenterClassname
			));
		}

		return $this->filterRules(
			$this->reader->getMethodAttributes($presenterClassname, Presenter::formatActionMethod(ucfirst($action)), Presenter::class)
		);
	}

	public function provideForSignal(string $componentClassname, string $signal): array
	{
		if (!is_subclass_of($componentClassname, Control::class, true)) {
			throw new InvalidArgumentException(sprintf(
				'Class %s is not a Control or Presenter.',
				$componentClassname
			));
		}

		return $this->filterRules(
			$this->reader->getMethodAttributes($componentClassname, Control::formatSignalMethod(ucfirst($signal)), Control::class)
		);
	}

	/**
	 * @param array<AttributeInfo> $attributes
	 *
	 * @return array<RuleInterface>
	 */
	private function filterRules(array $attributes): array
	{
		return array_values(
			array_filter(
				array_map(
					static fn (AttributeInfo $attributeInfo): AttributeInterface => $attributeInfo->attribute,
					$attributes
				),
				static fn (AttributeInterface $attribute): bool => $attribute instanceof RuleInterface
			)
		);
	}
}

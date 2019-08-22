<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Translator;

use Nette;

final class TranslatorDomain
{
	use Nette\StaticClass;

	/**
	 * @param string $className
	 *
	 * @return string
	 */
	public static function fromClassName(string $className): string
	{
		static $domains = [];

		if (isset($domains[$className])) {
			return $domains[$className];
		}

		$parts = explode('\\', $className);
		$shortName = array_pop($parts);

		$result = array_map(static function (string $part) {
			return str_replace('Module', '', $part);
		}, array_filter($parts, static function (string $part) {
			return Nette\Utils\Strings::endsWith($part, 'Module');
		}));

		return count($result)
			? implode('_', $result) . '_' . $shortName
			: $shortName;
	}
}

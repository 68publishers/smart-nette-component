<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\TemplateResolver;

use ReflectionClass;
use Nette\StaticClass;

final class TemplateFileResolverFactory
{
	use StaticClass;

	/** @var array  */
	private static $automaticCache = [];

	/**
	 * @param string $className
	 * @param string $path
	 *
	 * @return \SixtyEightPublishers\SmartNetteComponent\TemplateResolver\ManualTemplateFileResolver
	 */
	public static function create(string $className, string $path): ManualTemplateFileResolver
	{
		$resolver = self::getAutomaticResolver($className, $path);

		return new ManualTemplateFileResolver($resolver, $resolver->getMetadata());
	}

	/**
	 * @param string $className
	 * @param string $path
	 *
	 * @return \SixtyEightPublishers\SmartNetteComponent\TemplateResolver\AutomaticTemplateFileResolver
	 * @noinspection PhpDocMissingThrowsInspection
	 * @noinspection PhpUnhandledExceptionInspection
	 */
	private static function getAutomaticResolver(string $className, string $path): AutomaticTemplateFileResolver
	{
		if (!array_key_exists($key = md5($className . '=' . $path), self::$automaticCache)) {
			$reflection = new ReflectionClass($className);

			self::$automaticCache[$key] = new AutomaticTemplateFileResolver(new Metadata(
				$reflection->getName(),
				$reflection->getShortName(),
				is_dir($path) ? sprintf('%s/', rtrim($path, '\\/')) : sprintf('%s/%s/', dirname($reflection->getFileName()), trim($path, '\\/'))
			));
		}

		return self::$automaticCache[$key];
	}
}

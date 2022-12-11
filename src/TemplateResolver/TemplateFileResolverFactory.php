<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\TemplateResolver;

use ReflectionClass;
use function md5;
use function trim;
use function rtrim;
use function is_dir;
use function dirname;
use function sprintf;
use function array_key_exists;

final class TemplateFileResolverFactory
{
	private function __construct()
	{
	}

	/** @var array<string, AutomaticTemplateFileResolver> */
	private static array $automaticCache = [];

	public static function create(string $className, string $path): ManualTemplateFileResolver
	{
		$resolver = self::getAutomaticResolver($className, $path);

		return new ManualTemplateFileResolver($resolver, $resolver->getMetadata());
	}

	/**
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

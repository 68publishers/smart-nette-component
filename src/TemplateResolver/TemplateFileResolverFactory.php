<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\TemplateResolver;

use ReflectionClass;
use function md5;
use function trim;
use function is_dir;
use function dirname;
use function sprintf;
use function realpath;
use function file_exists;
use function array_key_exists;

final class TemplateFileResolverFactory
{
	private function __construct()
	{
	}

	/** @var array<string, AutomaticTemplateFileResolver> */
	private static array $automaticCache = [];

	/**
	 * @param class-string $classname
	 */
	public static function create(string $classname, string $path): ManualTemplateFileResolver
	{
		$resolver = self::getAutomaticResolver($classname, $path);

		return new ManualTemplateFileResolver($resolver, $resolver->getMetadata());
	}

	/**
	 * @param class-string $classname
	 *
	 * @noinspection PhpUnhandledExceptionInspection
	 * @noinspection PhpDocMissingThrowsInspection
	 */
	private static function getAutomaticResolver(string $classname, string $path): AutomaticTemplateFileResolver
	{
		if (!array_key_exists($key = md5($classname . '=' . $path), self::$automaticCache)) {
			$reflection = new ReflectionClass($classname);
			$realpath = realpath($path);

			self::$automaticCache[$key] = new AutomaticTemplateFileResolver(new Metadata(
				$reflection->getName(),
				$reflection->getShortName(),
				$realpath && file_exists($realpath) && is_dir($realpath) ? $realpath : sprintf('%s/%s', dirname((string) $reflection->getFileName()), trim($path, '\\/'))
			));
		}

		return self::$automaticCache[$key];
	}
}

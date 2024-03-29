<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\TemplateResolver;

use RuntimeException;
use Nette\Utils\Strings;
use function sprintf;
use function realpath;
use function file_exists;
use function array_key_exists;

final class AutomaticTemplateFileResolver implements TemplateFileResolverInterface
{
	/** @var array<string> */
	private array $templateFiles = [];

	public function __construct(
		private readonly Metadata $metadata
	) {
	}

	public function getMetadata(): Metadata
	{
		return $this->metadata;
	}

	public function resolve(string $type = ''): string
	{
		if (array_key_exists($type, $this->templateFiles)) {
			return $this->templateFiles[$type];
		}

		$typeString = empty($type) ? '' : ($type . '.');
		$paths = [
			sprintf('%s/%s%s.latte', $this->metadata->basePath, $typeString, $this->metadata->shortName),
			sprintf('%s/%s%s.latte', $this->metadata->basePath, $typeString, Strings::firstLower($this->metadata->shortName)),
		];

		foreach ($paths as $path) {
			if (true === file_exists($path)) {
				$path = realpath($path);

				if ($path) {
					$this->templateFiles[$type] = $path;

					return $path;
				}
			}
		}

		throw new RuntimeException(sprintf(
			'Can not find template file for component %s [type %s].',
			$this->metadata->name,
			true === empty($type) ? 'is default (empty)' : "'{$type}'"
		));
	}
}

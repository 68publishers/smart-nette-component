<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\TemplateResolver;

use SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException;
use function sprintf;
use function realpath;
use function file_exists;
use function array_key_exists;

final class ManualTemplateFileResolver implements TemplateFileResolverInterface
{
	/** @var array<string>  */
	private array $templateFiles = [];

	public function __construct(
		private readonly TemplateFileResolverInterface $fallback,
		private readonly Metadata $metadata
	) {
	}

	public function setFile(string $file, string $type = ''): void
	{
		if (false === file_exists($file)) {
			throw new InvalidStateException(sprintf(
				'Template file %s for component %s does not exists',
				$file,
				$this->metadata->name
			));
		}

		$this->templateFiles[$type] = realpath($file);
	}

	public function setRelativeFile(string $file, string $type = ''): void
	{
		$this->setFile(sprintf(
			'%s/%s',
			$this->metadata->basePath,
			$file
		), $type);
	}

	public function resolve(string $type = ''): string
	{
		if (array_key_exists($type, $this->templateFiles)) {
			return $this->templateFiles[$type];
		}

		return $this->fallback->resolve($type);
	}
}

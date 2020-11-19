<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\TemplateResolver;

use Nette\SmartObject;
use Nette\Utils\Strings;
use SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException;

final class AutomaticTemplateFileResolver implements TemplateFileResolverInterface
{
	use SmartObject;

	/** @var string[]  */
	private $templateFiles = [];

	/** @var \SixtyEightPublishers\SmartNetteComponent\TemplateResolver\Metadata  */
	private $metadata;

	/**
	 * @param \SixtyEightPublishers\SmartNetteComponent\TemplateResolver\Metadata $metadata
	 */
	public function __construct(Metadata $metadata)
	{
		$this->metadata = $metadata;
	}

	/**
	 * @return \SixtyEightPublishers\SmartNetteComponent\TemplateResolver\Metadata
	 */
	public function getMetadata(): Metadata
	{
		return $this->metadata;
	}

	/**
	 * @param string $file
	 * @param string $type
	 *
	 * @return void
	 */
	private function setFile(string $file, string $type = ''): void
	{
		if (FALSE === file_exists($file)) {
			throw new InvalidStateException(sprintf(
				'Template file %s for component %s does not exists',
				$file,
				$this->metadata->name
			));
		}

		$this->templateFiles[$type] = $file;
	}

	/******************** interface \SixtyEightPublishers\SmartNetteComponent\TemplateResolver\ITemplateFileResolver ********************/

	/**
	 * {@inheritdoc}
	 */
	public function resolve(string $type = ''): string
	{
		if (array_key_exists($type, $this->templateFiles)) {
			return $this->templateFiles[$type];
		}

		$typeString = TRUE === empty($type) ? $type : $type . '.';
		$paths = [
			sprintf('%s%s%s.latte', $this->metadata->basePath, $typeString, $this->metadata->shortName),
			sprintf('%s%s%s.latte', $this->metadata->basePath, $typeString, Strings::firstLower($this->metadata->shortName)),
		];

		foreach ($paths as $path) {
			if (TRUE === file_exists($path)) {
				$path = realpath($path);

				$this->setFile($path, $type);

				return $path;
			}
		}

		throw new InvalidStateException(sprintf(
			'Can not find template file for component %s [type %s]',
			$this->metadata->name,
			TRUE === empty($type) ? 'is default (empty)' : "'{$type}'"
		));
	}
}

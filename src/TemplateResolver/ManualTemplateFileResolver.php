<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\TemplateResolver;

use Nette\SmartObject;
use SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException;

final class ManualTemplateFileResolver implements TemplateFileResolverInterface
{
	use SmartObject;

	/** @var \SixtyEightPublishers\SmartNetteComponent\TemplateResolver\TemplateFileResolverInterface  */
	private $fallback;

	/** @var \SixtyEightPublishers\SmartNetteComponent\TemplateResolver\Metadata  */
	private $metadata;

	/** @var array  */
	private $templateFiles = [];

	/**
	 * @param \SixtyEightPublishers\SmartNetteComponent\TemplateResolver\TemplateFileResolverInterface $fallback
	 * @param \SixtyEightPublishers\SmartNetteComponent\TemplateResolver\Metadata                      $metadata
	 */
	public function __construct(TemplateFileResolverInterface $fallback, Metadata $metadata)
	{
		$this->fallback = $fallback;
		$this->metadata = $metadata;
	}

	/**
	 * @param string $file
	 * @param string $type
	 *
	 * @return void
	 */
	public function setFile(string $file, string $type = ''): void
	{
		if (FALSE === file_exists($file)) {
			throw new InvalidStateException(sprintf(
				'Template file %s for component %s does not exists',
				$file,
				$this->metadata->name
			));
		}

		$this->templateFiles[$type] = realpath($file);
	}

	/**
	 * @param string $file
	 * @param string $type
	 *
	 * @return void
	 */
	public function setRelativeFile(string $file, string $type = ''): void
	{
		$this->setFile(sprintf(
			'%s/%s',
			$this->metadata->basePath,
			$file
		), $type);
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

		return $this->fallback->resolve($type);
	}
}

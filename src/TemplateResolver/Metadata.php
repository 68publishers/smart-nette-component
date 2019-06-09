<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\TemplateResolver;

use Nette;

/**
 * @property-read string $name
 * @property-read string $shortName
 * @property-read string $basePath
 */
final class Metadata
{
	use Nette\SmartObject;

	/** @var string  */
	private $name;

	/** @var string  */
	private $shortName;

	/** @var string  */
	private $basePath;

	/**
	 * @param string $name
	 * @param string $shortName
	 * @param string $basePath
	 */
	public function __construct(string $name, string $shortName, string $basePath)
	{
		$this->name = $name;
		$this->shortName = $shortName;
		$this->basePath = $basePath;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getShortName(): string
	{
		return $this->shortName;
	}

	/**
	 * @return string
	 */
	public function getBasePath(): string
	{
		return $this->basePath;
	}
}

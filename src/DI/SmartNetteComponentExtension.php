<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\DI;

use Nette;
use Doctrine;
use SixtyEightPublishers;

final class SmartNetteComponentExtension extends Nette\DI\CompilerExtension
{
	/**
	 * {@inheritdoc}
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('reader'))
			->setType(SixtyEightPublishers\SmartNetteComponent\Reader\IAnnotationReader::class)
			->setFactory(SixtyEightPublishers\SmartNetteComponent\Reader\DoctrineAnnotationReader::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		if (NULL === $builder->getByType(Doctrine\Common\Annotations\Reader::class)) {
			throw new SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException(sprintf(
				'Missing service of type %s. Please register it manually or use one of suggested libraries from composer.json',
				Doctrine\Common\Annotations\Reader::class
			));
		}
	}
}

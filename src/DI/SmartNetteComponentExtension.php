<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\DI;

use Nette\DI\CompilerExtension;
use Doctrine\Common\Annotations\Reader;
use SixtyEightPublishers\SmartNetteComponent\Link\LinkAuthorizator;
use SixtyEightPublishers\SmartNetteComponent\Link\LinkAuthorizatorInterface;
use SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException;
use SixtyEightPublishers\SmartNetteComponent\Reader\DoctrineAnnotationReader;
use SixtyEightPublishers\SmartNetteComponent\Reader\AnnotationReaderInterface;

final class SmartNetteComponentExtension extends CompilerExtension
{
	/**
	 * {@inheritdoc}
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('reader'))
			->setType(AnnotationReaderInterface::class)
			->setFactory(DoctrineAnnotationReader::class);

		$builder->addDefinition($this->prefix('link_authorizator'))
			->setType(LinkAuthorizatorInterface::class)
			->setFactory(LinkAuthorizator::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		if (NULL === $builder->getByType(Reader::class)) {
			throw new InvalidStateException(sprintf(
				'Missing service of type %s. Please register it manually or use one of suggested libraries from composer.json',
				Reader::class
			));
		}
	}
}

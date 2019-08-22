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

		$builder->addDefinition($this->prefix('link_authorizator'))
			->setType(SixtyEightPublishers\SmartNetteComponent\Link\ILinkAuthorizator::class)
			->setFactory(SixtyEightPublishers\SmartNetteComponent\Link\LinkAuthorizator::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$translator = $builder->getByType(Nette\Localization\ITranslator::class, FALSE);

		if (NULL === $builder->getByType(Doctrine\Common\Annotations\Reader::class)) {
			throw new SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException(sprintf(
				'Missing service of type %s. Please register it manually or use one of suggested libraries from composer.json',
				Doctrine\Common\Annotations\Reader::class
			));
		}

		/** @var \Nette\DI\ServiceDefinition[] $translatableServices */
		$translatableServices = array_filter($builder->getDefinitions(), static function (Nette\DI\ServiceDefinition $def) {
			return is_a($def->getImplement(), SixtyEightPublishers\SmartNetteComponent\Translator\ITranslatorAware::class, TRUE)
				|| ($def->getImplementMode() !== $def::IMPLEMENT_MODE_GET && is_a($def->getType(), SixtyEightPublishers\SmartNetteComponent\Translator\ITranslatorAware::class, TRUE));
		});

		if (NULL === $translator && 0 < count($translatableServices)) {
			throw new SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException(sprintf(
				'There %s that implements interface %s but service of type %s is not registered.',
				1 === count($translatableServices) ? 'is 1 service' : ('are ' . count($translatableServices) . ' services'),
				SixtyEightPublishers\SmartNetteComponent\Translator\ITranslatorAware::class,
				Nette\Localization\ITranslator::class
			));
		}

		$translator = $builder->getDefinition($translator);

		foreach ($translatableServices as $translatableService) {
			$translatableService->addSetup('setTranslator', [
				'translator' => $translator,
			]);
		}
	}
}

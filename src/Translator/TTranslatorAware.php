<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Translator;

use Nette;
use SixtyEightPublishers;

trait TTranslatorAware
{
	/** @var NULL|\Nette\Localization\ITranslator */
	private $translator;

	/** @var NULL|\Nette\Localization\ITranslator */
	private $prefixedTranslator;

	/**
	 * You can override this method with some custom strategy.
	 *
	 * @return string
	 */
	protected function createPrefixedTranslatorDomain(): string
	{
		return TranslatorDomain::fromClassName(static::class);
	}

	/**
	 * @param \Nette\Localization\ITranslator $translator
	 *
	 * @return void
	 */
	public function setTranslator(Nette\Localization\ITranslator $translator): void
	{
		$this->translator = $translator;
		$this->prefixedTranslator = NULL;
	}

	/**
	 * @return \Nette\Localization\ITranslator
	 *
	 * @return void
	 * @throws \SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException
	 */
	public function getTranslator(): Nette\Localization\ITranslator
	{
		if (NULL === $this->translator) {
			throw new SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException(sprintf(
				'Translator is not set. Use interface %s if injects are enabled for this class or set it manually via %s::setTranslator().',
				ITranslatorAware::class,
				static::class
			));
		}

		return $this->translator;
	}

	/**
	 * @return \Nette\Localization\ITranslator
	 */
	public function getPrefixedTranslator(): Nette\Localization\ITranslator
	{
		if (NULL === $this->prefixedTranslator) {
			$translator = $this->getTranslator();
			$domain = $this->createPrefixedTranslatorDomain();
			$kdybyTranslatorClassName = 'Kdyby\Translation\Translator';

			/** @noinspection PhpUndefinedMethodInspection */
			$this->prefixedTranslator = $translator instanceof $kdybyTranslatorClassName
				? $this->translator->domain($domain)
				: new PrefixedTranslator($translator, $domain);
		}

		return $this->prefixedTranslator;
	}
}

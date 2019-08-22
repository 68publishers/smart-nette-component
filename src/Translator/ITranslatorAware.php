<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Translator;

use Nette;

interface ITranslatorAware
{
	/**
	 * @param \Nette\Localization\ITranslator $translator
	 *
	 * @return void
	 */
	public function setTranslator(Nette\Localization\ITranslator $translator): void;

	/**
	 * @return \Nette\Localization\ITranslator
	 *
	 * @return void
	 */
	public function getTranslator(): Nette\Localization\ITranslator;
}

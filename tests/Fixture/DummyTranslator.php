<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Fixture;

use Nette;

final class DummyTranslator implements Nette\Localization\ITranslator
{
	/***************** interface \Nette\Localization\ITranslator *****************/

	/**
	 * {@inheritdoc}
	 */
	public function translate($message, $count = NULL): string
	{
		return (string) $message;
	}
}

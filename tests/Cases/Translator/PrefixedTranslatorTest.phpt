<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Cases\Translator;

use Tester;
use SixtyEightPublishers;

require __DIR__ . '/../../bootstrap.php';

final class PrefixedTranslatorTest extends Tester\TestCase
{
	/**
	 * @return void
	 */
	public function testBase(): void
	{
		$translator = new SixtyEightPublishers\SmartNetteComponent\Translator\PrefixedTranslator(
			new SixtyEightPublishers\SmartNetteComponent\Tests\Fixture\DummyTranslator(),
			'prefix'
		);

		Tester\Assert::same('prefix.message', $translator->translate('message'));
	}
}

(new PrefixedTranslatorTest())->run();

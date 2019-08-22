<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Cases\Translator;

use Tester;
use SixtyEightPublishers;

require __DIR__ . '/../../bootstrap.php';

final class TranslatorDomainTest extends Tester\TestCase
{
	/**
	 * @return void
	 */
	public function testBase(): void
	{
		Tester\Assert::same(
			'MyClass',
			SixtyEightPublishers\SmartNetteComponent\Translator\TranslatorDomain::fromClassName('Test\\My\\Awesome\\NS\\MyClass')
		);

		Tester\Assert::same(
			'Front_MyComponent',
			SixtyEightPublishers\SmartNetteComponent\Translator\TranslatorDomain::fromClassName('Test\\FrontModule\\MyComponent')
		);

		Tester\Assert::same(
			'Front_Homepage_MyPresenter',
			SixtyEightPublishers\SmartNetteComponent\Translator\TranslatorDomain::fromClassName('Test\\FrontModule\\HomepageModule\\Presenter\\MyPresenter')
		);
	}
}

(new TranslatorDomainTest())->run();

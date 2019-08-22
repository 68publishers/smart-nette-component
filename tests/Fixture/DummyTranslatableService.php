<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Fixture;

use SixtyEightPublishers;

final class DummyTranslatableService implements SixtyEightPublishers\SmartNetteComponent\Translator\ITranslatorAware
{
	use SixtyEightPublishers\SmartNetteComponent\Translator\TTranslatorAware;
}

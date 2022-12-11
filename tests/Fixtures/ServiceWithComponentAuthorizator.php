<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Fixtures;

use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizatorAwareTrait;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizatorAwareInterface;

final class ServiceWithComponentAuthorizator implements ComponentAuthorizatorAwareInterface
{
	use ComponentAuthorizatorAwareTrait;
}

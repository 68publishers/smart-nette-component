<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Cases\TemplateResolver;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixture\EmptyClass;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\Metadata;
use SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\AutomaticTemplateFileResolver;

require __DIR__ . '/../../bootstrap.php';

final class AutomaticTemplateFileResolverTest extends TestCase
{
	/** @var \SixtyEightPublishers\SmartNetteComponent\TemplateResolver\AutomaticTemplateFileResolver */
	private $resolver;

	/**
	 * @return void
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$metadata = new Metadata(
			EmptyClass::class,
			'EmptyClass',
			__DIR__ . '/../../Fixture/templates/'
		);

		$this->resolver = new AutomaticTemplateFileResolver($metadata);
	}

	/**
	 * @return void
	 */
	public function testResolveDefaultFile(): void
	{
		Assert::same(realpath(__DIR__ . '/../../Fixture/templates/emptyClass.latte'), $this->resolver->resolve());
	}

	/**
	 * @return void
	 */
	public function testResolveNamedFile(): void
	{
		Assert::same(realpath(__DIR__ . '/../../Fixture/templates/second.emptyClass.latte'), $this->resolver->resolve('second'));
		Assert::same(realpath(__DIR__ . '/../../Fixture/templates/upper.EmptyClass.latte'), $this->resolver->resolve('upper'));
	}

	/**
	 * @return void
	 */
	public function testThrowExceptionOnMissingFile(): void
	{
		Assert::exception(
			function () {
				$this->resolver->resolve('third');
			},
			InvalidStateException::class,
			'Can not find template file for component SixtyEightPublishers\SmartNetteComponent\Tests\Fixture\EmptyClass [type \'third\']'
		);
	}
}

(new AutomaticTemplateFileResolverTest())->run();

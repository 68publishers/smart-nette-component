<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Cases\TemplateResolver;

use Tester;
use SixtyEightPublishers;

require __DIR__ . '/../../bootstrap.php';

final class AutomaticTemplateFileResolverTest extends Tester\TestCase
{
	/** @var \SixtyEightPublishers\SmartNetteComponent\TemplateResolver\AutomaticTemplateFileResolver */
	private $resolver;

	/**
	 * @return void
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$metadata = new SixtyEightPublishers\SmartNetteComponent\TemplateResolver\Metadata(
			SixtyEightPublishers\SmartNetteComponent\Tests\Fixture\EmptyClass::class,
			'EmptyClass',
			__DIR__ . '/../../Fixture/templates/'
		);

		$this->resolver = new SixtyEightPublishers\SmartNetteComponent\TemplateResolver\AutomaticTemplateFileResolver($metadata);
	}

	/**
	 * @return void
	 */
	public function testResolveDefaultFile(): void
	{
		Tester\Assert::same(realpath(__DIR__ . '/../../Fixture/templates/emptyClass.latte'), $this->resolver->resolve());
	}

	/**
	 * @return void
	 */
	public function testResolveNamedFile(): void
	{
		Tester\Assert::same(realpath(__DIR__ . '/../../Fixture/templates/second.emptyClass.latte'), $this->resolver->resolve('second'));
		Tester\Assert::same(realpath(__DIR__ . '/../../Fixture/templates/upper.EmptyClass.latte'), $this->resolver->resolve('upper'));
	}

	/**
	 * @return void
	 */
	public function testThrowExceptionOnMissingFile(): void
	{
		Tester\Assert::exception(
			function () {
				$this->resolver->resolve('third');
			},
			SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException::class,
			'Can not find template file for component SixtyEightPublishers\SmartNetteComponent\Tests\Fixture\EmptyClass [type \'third\']'
		);
	}
}

(new AutomaticTemplateFileResolverTest())->run();

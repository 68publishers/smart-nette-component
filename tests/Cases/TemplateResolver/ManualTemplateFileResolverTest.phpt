<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Cases;

use Tester;
use Mockery;
use SixtyEightPublishers;

require __DIR__ . '/../../bootstrap.php';

final class ManualTemplateFileResolverTest extends Tester\TestCase
{
	/** @var \SixtyEightPublishers\SmartNetteComponent\TemplateResolver\ManualTemplateFileResolver */
	private $resolver;

	/**
	 * @return void
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$fallbackResolver = Mockery::mock(SixtyEightPublishers\SmartNetteComponent\TemplateResolver\ITemplateFileResolver::class);
		$fallbackResolver->shouldReceive('resolve')->with('foo')->andReturn('FALLBACK_FOO');

		$metadata = new SixtyEightPublishers\SmartNetteComponent\TemplateResolver\Metadata(
			SixtyEightPublishers\SmartNetteComponent\Tests\Fixture\EmptyClass::class,
			'EmptyClass',
			__DIR__ . '/../../Fixture'
		);

		$this->resolver = new SixtyEightPublishers\SmartNetteComponent\TemplateResolver\ManualTemplateFileResolver(
			$fallbackResolver,
			$metadata
		);
	}

	/**
	 * @return void
	 */
	protected function tearDown(): void
	{
		parent::tearDown();

		Mockery::close();
	}

	/**
	 * @return void
	 */
	public function testSetValidTemplateFile(): void
	{
		Tester\Assert::noError(function () {
			$this->resolver->setFile(__DIR__ . '/../../Fixture/templates/emptyClass.latte');
		});
	}

	/**
	 * @return void
	 */
	public function testSetValidRelativeTemplateFile(): void
	{
		Tester\Assert::noError(function () {
			$this->resolver->setRelativeFile('templates/second.emptyClass.latte');
		});
	}

	/**
	 * @return void
	 */
	public function testThrowExceptionOnSetMissingTemplateFile(): void
	{
		Tester\Assert::exception(
			function () {
				$this->resolver->setFile(__DIR__ . '/foo.latte');
			},
			SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException::class,
			sprintf('Template file %s/foo.latte for component SixtyEightPublishers\SmartNetteComponent\Tests\Fixture\EmptyClass does not exists', __DIR__)
		);
	}

	/**
	 * @return void
	 */
	public function testThrowExceptionOnSetMissingRelativeTemplateFile(): void
	{
		Tester\Assert::exception(
			function () {
				$this->resolver->setRelativeFile('templates/emptyClass.third.latte');
			},
			SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException::class,
			sprintf('Template file %s/templates/emptyClass.third.latte for component SixtyEightPublishers\SmartNetteComponent\Tests\Fixture\EmptyClass does not exists', __DIR__ . '/../../Fixture')
		);
	}

	/**
	 * @return void
	 */
	public function testResolveFile(): void
	{
		$firstFile = __DIR__ . '/../../Fixture/templates/emptyClass.latte';
		$secondFile = __DIR__ . '/../../Fixture/templates/second.emptyClass.latte';

		$this->resolver->setFile($firstFile);
		$this->resolver->setFile($secondFile, 'second');

		Tester\Assert::same(realpath($firstFile), $this->resolver->resolve());
		Tester\Assert::same(realpath($secondFile), $this->resolver->resolve('second'));
		Tester\Assert::same('FALLBACK_FOO', $this->resolver->resolve('foo'));
	}
}

(new ManualTemplateFileResolverTest())->run();

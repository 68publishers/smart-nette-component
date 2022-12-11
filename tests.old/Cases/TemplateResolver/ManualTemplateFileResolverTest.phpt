<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Tests\Cases\TemplateResolver;

use Mockery;
use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\SmartNetteComponent\Tests\Fixture\EmptyClass;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\Metadata;
use SixtyEightPublishers\SmartNetteComponent\Exception\InvalidStateException;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\ManualTemplateFileResolver;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\TemplateFileResolverInterface;

require __DIR__ . '/../../bootstrap.php';

final class ManualTemplateFileResolverTest extends TestCase
{
	/** @var \SixtyEightPublishers\SmartNetteComponent\TemplateResolver\ManualTemplateFileResolver */
	private $resolver;

	/**
	 * @return void
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$fallbackResolver = Mockery::mock(TemplateFileResolverInterface::class);
		$fallbackResolver->shouldReceive('resolve')->with('foo')->andReturn('FALLBACK_FOO');

		$metadata = new Metadata(
			EmptyClass::class,
			'EmptyClass',
			__DIR__ . '/../../Fixture'
		);

		$this->resolver = new ManualTemplateFileResolver(
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
		Assert::noError(function () {
			$this->resolver->setFile(__DIR__ . '/../../Fixture/templates/emptyClass.latte');
		});
	}

	/**
	 * @return void
	 */
	public function testSetValidRelativeTemplateFile(): void
	{
		Assert::noError(function () {
			$this->resolver->setRelativeFile('templates/second.emptyClass.latte');
		});
	}

	/**
	 * @return void
	 */
	public function testThrowExceptionOnSetMissingTemplateFile(): void
	{
		Assert::exception(
			function () {
				$this->resolver->setFile(__DIR__ . '/foo.latte');
			},
			InvalidStateException::class,
			sprintf('Template file %s/foo.latte for component SixtyEightPublishers\SmartNetteComponent\Tests\Fixture\EmptyClass does not exists', __DIR__)
		);
	}

	/**
	 * @return void
	 */
	public function testThrowExceptionOnSetMissingRelativeTemplateFile(): void
	{
		Assert::exception(
			function () {
				$this->resolver->setRelativeFile('templates/emptyClass.third.latte');
			},
			InvalidStateException::class,
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

		Assert::same(realpath($firstFile), $this->resolver->resolve());
		Assert::same(realpath($secondFile), $this->resolver->resolve('second'));
		Assert::same('FALLBACK_FOO', $this->resolver->resolve('foo'));
	}
}

(new ManualTemplateFileResolverTest())->run();

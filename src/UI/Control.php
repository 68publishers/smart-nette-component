<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\UI;

use Nette\Application\UI\Control as NetteControl;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\ManualTemplateFileResolver;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\TemplateFileResolverFactory;

abstract class Control extends NetteControl
{
	use AnnotatedControlTrait;

	/** @var NULL|\SixtyEightPublishers\SmartNetteComponent\TemplateResolver\ManualTemplateFileResolver */
	private $templateFileResolver;

	/**
	 * {@inheritdoc}
	 * @throws \SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException
	 */
	public function checkRequirements($element): void
	{
		parent::checkRequirements($element);

		# Process Signal (handle) method's annotations. Called in Component::tryCall()
		if ($element instanceof \ReflectionMethod) {
			$this->checkAuthorizationOnMethod($element);
		}
	}

	/**
	 * @return void
	 */
	protected function beforeRender(): void
	{
	}

	/**
	 * @param string $type
	 *
	 * @return void
	 */
	protected function doRender(string $type = ''): void
	{
		$this->template->setFile($this->getTemplateFileResolver()->resolve($type));
		$this->beforeRender();
		$this->template->render();
	}

	/**
	 * @param string $file
	 * @param string $type
	 *
	 * @return $this
	 */
	public function setFile(string $file, string $type = '')
	{
		$this->getTemplateFileResolver()->setFile($file, $type);

		return $this;
	}

	/**
	 * @param string $file
	 * @param string $type
	 *
	 * @return $this
	 */
	public function setRelativeFile(string $file, string $type = '')
	{
		$this->getTemplateFileResolver()->setRelativeFile($file, $type);

		return $this;
	}

	/**
	 * @return \SixtyEightPublishers\SmartNetteComponent\TemplateResolver\ManualTemplateFileResolver
	 */
	private function getTemplateFileResolver(): ManualTemplateFileResolver
	{
		return $this->templateFileResolver
			?? $this->templateFileResolver = TemplateFileResolverFactory::create(static::class, 'templates');
	}
}

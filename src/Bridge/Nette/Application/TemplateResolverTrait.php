<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\Application;

use Nette\Application\UI\Template;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\ManualTemplateFileResolver;
use SixtyEightPublishers\SmartNetteComponent\TemplateResolver\TemplateFileResolverFactory;

/**
 * For Controls only
 *
 * @method Template getTemplate()
 */
trait TemplateResolverTrait
{
	private ?ManualTemplateFileResolver $templateFileResolver = null;

	public function setFile(string $file, string $type = ''): self
	{
		$this->getTemplateFileResolver()->setFile($file, $type);

		return $this;
	}

	public function setRelativeFile(string $file, string $type = ''): self
	{
		$this->getTemplateFileResolver()->setRelativeFile($file, $type);

		return $this;
	}

	public function render(): void
	{
		$this->doRender();
	}

	protected function beforeRender(): void
	{
	}

	protected function doRender(string $type = ''): void
	{
		$this->getTemplate()->setFile($this->getTemplateFileResolver()->resolve($type));
		$this->beforeRender();
		$this->getTemplate()->render();
	}

	protected function doRenderToString(string $type = ''): string
	{
		$this->getTemplate()->setFile($this->getTemplateFileResolver()->resolve($type));
		$this->beforeRender();

		return $this->getTemplate()->renderToString();
	}

	protected function getTemplateFileResolver(): ManualTemplateFileResolver
	{
		return $this->templateFileResolver
			?? $this->templateFileResolver = TemplateFileResolverFactory::create(static::class, 'templates');
	}
}

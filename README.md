# Smart Nette Components

This package adds some useful features for Nette Presenters and Components like:

- authorization annotations over `Presenter` classes, `action*` and `handle*` methods
- authorization annotations over `handle*` methods in `Control` classes
- resolving of template files for components

## Installation

The best way to install 68publishers/smart-nette-component is using Composer:

```bash
composer require 68publishers/smart-nette-component
```

then you can register extension into DIC:

```yaml
extensions:
    smart_nette_component: SixtyEightPublishers\SmartNetteComponent\DI\SmartNetteComponentExtension
```

:exclamation: You also need service of type `Doctrine\Common\Annotations\Reader` registered in DI Container. 
This package doesn't force you to use any particular implementation, so you can use one of suggested packages (see `composer.json`) or another/manual integration.

## Usage

### Annotations

simple authorization:

```php
use SixtyEightPublishers\SmartNetteComponent\Annotation as A;

/**
 * @A\LoggedIn()
 */
class SecuredPresenter extends SixtyEightPublishers\SmartNetteComponent\UI\Presenter
{
}

/**
 * @A\IsInRole(name="foo")
 */
class MoreSecuredPresenter extends SixtyEightPublishers\SmartNetteComponent\UI\Presenter
{
	/**
	 * @A\IsAllowed(resource="foo_resource", privilege="bar")
	 */
	public function actionBar() : void
	{
	}

	/**
	 * @A\IsAllowed(resource="foo_resource", privilege="baz")
	 */
	public function handleDoBaz() : void
	{
	}
	
	/**
	 * By default the exception `SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException` is thrown.
	 * You can override this method and set flash-message or redirect here:
	 *
	 * {@inheritdoc}
	 */
	protected function onForbiddenRequest(SixtyEightPublishers\SmartNetteComponent\Annotation\IAuthorizationAnnotation $annotation): void
	{
		$this->flashMessage('...');
		$this->redirect('...');
	}
}
```

you can also set layout file via annotation:

```php
/**
 * Path is relative from presenter location
 *
 * @A\Layount(path="templates/Foo/@myLayout.latte")
 */
class FooPresenter extends SixtyEightPublishers\SmartNetteComponent\UI\Presenter
{
}
```

simple authorization in components:

```php
class FooControl extends SixtyEightPublishers\SmartNetteComponent\UI\Control
{
	/**
	 * @A\IsLoggedIn()
	 * @A\IsAllowed(resource="foo_resource", privilege="baz")
	 */
	public function handleDoBaz() : void
	{
	}

	/**
	 * You can use this method in same way as in Presenter
	 *
	 * {@inheritdoc}
	 */
	protected function onForbiddenRequest(SixtyEightPublishers\SmartNetteComponent\Annotation\IAuthorizationAnnotation $annotation): void
	{
		$this->flashMessage('...');
		$this->presenter->redirect('...');
	}
}
```

### Template resolving

```php
class FooControl extends SixtyEightPublishers\SmartNetteComponent\UI\Control
{
	/**
	 * @return void
	 */
	public function render() : void
	{
		# ...
		$this->doRender();
	}

	/**
	 * @return void
	 */
	public function renderBar() : void
	{
		# ...
		$this->doRender('bar');
	}
}
```

Template for base render method will be resolved as `COMPONENT_DIRECTORY/templates/fooControl.latte` or `COMPONENT_DIRECTORY/templates/FooControl.latte`.

Template for `bar` render method will be resolved as `COMPONENT_DIRECTORY/templates/bar.fooControl.latte` or `COMPONENT_DIRECTORY/templates/bar.FooControl.latte`.

Of course you can set template file manually:

```php
class BarPresenter extends SixtyEightPublishers\SmartNetteComponent\UI\Presenter
{
	protected function createComponentFoo() : FooControl
	{
		$control = new FooControl();
		
		$control->setFile(__DIR__ . '/path/to/file.latte');
		# or relatively from Component's directory:
		$control->setRelativeFile('templates/new/fooControl.latte');
		
		# You can change template for specific render type:
		$control->setFile(__DIR__ . '/path/to/bazFile.latte', 'baz'); # template for `renderBaz()`
		
		return $control;
	}
}
```

## Contributing

Before committing any changes, don't forget to run

```bash
vendor/bin/php-cs-fixer fix --config=.php_cs.dist -v --dry-run
```

and

```bash
vendor/bin/tester ./tests
```

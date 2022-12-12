<h1 align="center">Smart Nette Component</h1>

<p align="center">This package adds some useful features for Nette Presenters and Components, such as authorization for Presenters, their actions and signals using PHP8 attributes, authorization for component signals using attributes, and resolving of component template files.</p>

<p align="center">
<a href="https://github.com/68publishers/smart-nette-component/actions"><img alt="Checks" src="https://badgen.net/github/checks/68publishers/smart-nette-component/master"></a>
<a href="https://coveralls.io/github/68publishers/smart-nette-component?branch=master"><img alt="Coverage Status" src="https://coveralls.io/repos/github/68publishers/smart-nette-component/badge.svg?branch=master"></a>
<a href="https://packagist.org/packages/68publishers/smart-nette-component"><img alt="Total Downloads" src="https://badgen.net/packagist/dt/68publishers/smart-nette-component"></a>
<a href="https://packagist.org/packages/68publishers/smart-nette-component"><img alt="Latest Version" src="https://badgen.net/packagist/v/68publishers/smart-nette-component"></a>
<a href="https://packagist.org/packages/68publishers/smart-nette-component"><img alt="PHP Version" src="https://badgen.net/packagist/php/68publishers/smart-nette-component"></a>
</p>

## Installation

The best way to install 68publishers/smart-nette-component is using Composer:

```sh
$ composer require 68publishers/smart-nette-component
```

## Authorization attributes

To use the authorization attributes, you need to register a compiler extension.

```neon
extensions:
    component_authorization: SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\DI\ComponentAuthorizationExtension

# The default configuration (you don't need to define it) is as follows:
component_authorization:
    cache: %debugMode%
    scanDirs:
    	- %appDir%
    scanComposer: yes
    scanFilters:
    	- *Presenter
    	- *Control
    	- *Component
```

Attributes can be cached when folding the DI container to avoid using reflection at runtime.
The extension will create a classmap and a map of all attributes automatically, it just needs to know where to look for Presenters and Components.
This is done with the `scanDirs`, `scanComposer` and `scanFilters` options, which behave similarly to [nette/application](https://doc.nette.org/en/application/configuration#toc-automatic-registration-of-presenters).

Now add the following trait to your `BasePresenter` and `BaseControl`:

```php
use Nette\Application\UI\Presenter;
use SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\Application\AuthorizationTrait;

abstract class BasePresenter extends Presenter
{
    use AuthorizationTrait;
}
```

```php
use Nette\Application\UI\Control;
use SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\Application\AuthorizationTrait;

abstract class BaseControl extends Control
{
    use AuthorizationTrait;
}
```

From now, you can use authorization attributes in your Presenters and Components:

```php
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedIn;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[LoggedIn]
final class AdminProductPresenter extends BasePresenter
{
    #[Allowed('product_resource', 'add')]
    public function actionAdd(): void {}

    #[Allowed('product_resource', 'delete')]
    public function handleDelete(): void {}
}
```

```php
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedIn;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

final class EditOrderControl extends BaseControl
{
    #[Allowed('order_resource', 'delete_item')]
    public function handleDeleteItem(): void {}
}
```

The Presenter/Component throws the exception `SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException` if any of the conditions in the attributes are not met.

The package includes the following attributes:

- `Allowed`
- `InRole`
- `LoggedIn`
- `LoggedOut`

If you would like to react somehow to the thrown exception, you can overwrite the `onForbiddenRequest()` method in a Presenter/Component.

```php
protected function onForbiddenRequest(ForbiddenRequestException $exception): void
{
    # `$exception->rule` contains failed attribute
    
    $this->flashMessage('You don\'t have access here!', 'error');
    $this->redirect('Homepage:');
}
```

### Custom authorization attributes

You can register your own attributes in the following way:

```php
use SixtyEightPublishers\SmartNetteComponent\Attribute\AttributeInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleInterface;
use SixtyEightPublishers\SmartNetteComponent\Authorization\RuleHandlerInterface;
use SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException;

final class CustomRule implements AttributeInterface, RuleInterface
{
    # ...
}

final class CustomRuleHandler implements RuleHandlerInterface
{
    public function canHandle(RuleInterface $rule): bool
    {
        return $rule instanceof CustomRule;
    }

    public function __invoke(RuleInterface $rule): void
    {
        assert($rule instanceof CustomRule);

        if (...) {
            throw new ForbiddenRequestException($rule);
        }
    }
}
```

```neon
services:
    -
        autowired: no
        factory: CustomRuleHandler
```

## Template resolving

You don't need to register any compiler extension to use this feature, just use the `TemplateResolverTrait` trait in your BaseControl.

```php
use Nette\Application\UI\Control;
use SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\Application\TemplateResolverTrait;

abstract class BaseControl extends Control
{
    use TemplateResolverTrait;
}
```

The base `render()` method is already declared in the trait.
To assign variables to the template, we can use the `beforeRender()` method.
You can also define custom `render*()` methods that are called for rendering using `{control myControl:foo}`.

```php
final class MyControl extends BaseControl
{
    protected function beforeRender(): void
    {
        # assign variables into the template here
    }

    public function renderFoo(): void
    {
        $this->doRender('foo');
    }
}
```

The template for the base render method will be resolved as `COMPONENT_DIRECTORY/templates/myControl.latte` or `COMPONENT_DIRECTORY/templates/MyControl.latte`.

The template for the `foo` render method will be resolved as `COMPONENT_DIRECTORY/templates/foo.myControl.latte` or `COMPONENT_DIRECTORY/templates/foo.MyControl.latte`.

Of course, you can set a template file manually:

```php
final class MyPresenter extends BasePresenter
{
    protected function createComponentMyControl() : FooControl
    {
        $control = $this->myControlFactory->create();

        $control->setFile(__DIR__ . '/path/to/file.latte');
        # or relatively from a component's directory:
        $control->setRelativeFile('templates/new/myControl.latte');

        # you can change the template for a specific render type:
        $control->setFile(__DIR__ . '/path/to/myControl.latte', 'foo'); # template for `renderFoo()`

        return $control;
    }
}
```

## Contributing

Before opening a pull request, please check your changes using the following commands

```bash
$ make init # to pull and start all docker images

$ make cs.check
$ make stan
$ make tests.all
```

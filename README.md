# Installing

```bash
composer require piano/mvc
```

# Creating a basic project structure

```bash
composer create-project piano/mvc project-name dev-project
```

# Controller

#### Redirecting

```php
$this->redirect('/module/controller/action');
```

#### Redirecting with variables

```php
$this->redirect(
    '/module/controller/action',
    [
        'firstName' => 'Diogo',
        'lastName' => 'Cavilha',
    ]
);
```

#### Getting params by name

```php
$id = $this->getParam('id');
```

#### Getting all params

```php
$params = $this->getParams();
```

#### Methods:

##### initialize()

If you need to create a constructor method for your controller, you can do this by creating a method called `initialize()` instead of `__construct()`.
The `__construct()` method is already being used by `Piano\Mvc\Controller`.

```php
<?php

namespace app\modules\application\controllers;

class IndexController extends Piano\Mvc\Controller
{
    protected function initialize()
    {
        // Do some action before executing any other code of your controller.
    }
}
```

-----------------------------------------------

# View

#### Rendering a view

```php
$this->view->render('view-name');
```

#### Rendering a view with variables

```php
$this->view->render('view-name', ['name' => 'Diogo']);
```

#### Disabling/Enabling the layout

```php
$this->view->disableLayout(); // disabling
$this->view->disableLayout(true); // disabling
$this->view->disableLayout(false); // enabling
```

#### Adding a partial

```php
$this->partial('/path/to/file');
```

#### Adding a partial with variables

```php
$this->partial('/path/to/file', ['title' => '']);
```

#### writing href links

```
<a href="<?php echo $this->url('route_name'); ?>">Text</a>
```

#### Form action must be written with a pre-defined route

```
<form action="<?php echo $this->url('contact'); ?>" method="post">
    Name: <input type="text" name="name">
    Email: <input type="text" name="email">
    <input type="submit" name="Send">
</form>
```
-----------------------------------------------

# Application

#### Getting module name

```php
$this->getApplication()->getModuleName();
```

#### Getting controller name

```php
$this->getApplication()->getControllerName();
```

#### Getting action name

```php
$this->getApplication()->getActionName();
```
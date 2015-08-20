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

So, in the view code you can access the variable:

```php
<p>The user name is: <?php echo $name ?></p>
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
$this->partial('/path/to/file', ['title' => 'Piano MVC rocks!']);
```

#### Loading CSS files

You can load CSS files on demand. It means you can define what CSS files are gonna be loaded when some specific view is rendered.
Better than that, you can do this for each of your views.


You can use the `addCss()` method.

```php
$this->view->addCss('/path/to/file1.css');
$this->view->addCss('/path/to/file2.css');
$this->view->addCss('/path/to/file3.css');

$this->view->render('view-name');

// or

$this->view
    ->addCss('/path/to/file1.css')
    ->addCss('/path/to/file2.css')
    ->addCss('/path/to/file3.css')
    ->render('view-name');
```

Or you may want to use the `setCss()` method.

```php
$this->view->setCss([
    '/path/to/file1.css',
    '/path/to/file2.css',
    '/path/to/file3.css',
]);

$this->view->render('view-name');

// or

$this->view
    ->setCss([
        '/path/to/file1.css',
        '/path/to/file2.css',
        '/path/to/file3.css',
    ])
    ->render('view-name');

```

> PS: You must call `addCss()` or `setCss()` method before calling the `render()` method. Otherwise it won't work.

#### Loading javascript files

You can load javascript files on demand. It means you can define what javascript files are gonna be loaded when some specific view is rendered.
Better than that, you can do this for each of your views.


You can use the `addJs()` method.

```php
$this->view->addJs('/path/to/file1.js');
$this->view->addJs('/path/to/file2.js');
$this->view->addJs('/path/to/file3.js');

$this->view->render('view-name');

// or

$this->view
    ->addJs('/path/to/file1.js')
    ->addJs('/path/to/file2.js')
    ->addJs('/path/to/file3.js')
    ->render('view-name');
```

Or you may want to use the `setJs()` method.

```php
$this->view->setJs([
    '/path/to/file1.js',
    '/path/to/file2.js',
    '/path/to/file3.js',
]);

$this->view->render('view-name');

// or

$this->view
    ->setJs([
        '/path/to/file1.js',
        '/path/to/file2.js',
        '/path/to/file3.js',
    ])
    ->render('view-name');

```

> PS: You must call `addJs()` or `setJs()` method before calling the `render()` method. Otherwise it won't work.

In order to load these CSS or JS files in your view/layout you can call the `loadCss()` or `loadJs()` method.

```php
// Loading the js files
$this->loadJs();

// Loading the css files
$this->loadCss();
```

#### href links

```
<a href="<?php echo $this->url('route_name'); ?>">Text</a>
```

#### Form action must be written by using a pre-defined route

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
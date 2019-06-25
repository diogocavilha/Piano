[![Build Status](https://travis-ci.org/diogocavilha/Piano.svg?branch=master)](https://travis-ci.org/diogocavilha/Piano)

# Piano MVC

This is not a micro/mini/whatever framework nor claims to be.

It's only a very simple MVC structure.

> WARNING: This has been just like a lab. I don't update this project anymore. If you want to use it, the ball is on your court.

# Creating the project structure

```bash
composer create-project piano/mvc myProject dev-project
```

## Controller

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

## Methods from `Piano\Mvc\Controller`

##### initialize()

If you need to create a constructor method for your controller, you can do this by creating a method called `initialize()` instead of `__construct()`.
The `__construct()` method is already being used by `Piano\Mvc\Controller`.

#### Example:

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

## View

#### Rendering a view

```php
$this->view->render('view-name');
```

#### Rendering a view with variables

```php
$this->view->render('view-name', ['name' => 'Diogo']);

// or

$this->view->addVar('name', 'Diogo');
$this->view->addVar('email', 'email@domain.com');

$this->view->render('view-name');

// or

$this->view->setVars([
    'name' => 'Diogo',
    'email' => 'email@domain.com',
]);

$this->view->render('view-name');
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

## Model

#### The DataAccessAbstract class.

The `Piano\Mvc\DataAccessAbstract` abstract class provides us a few methods for handling data by accessing the database.

#### Attributes from `Piano\Mvc\DataAccessAbstract`

```php
protected $table;
```
> Table name for working.

```php
protected $model;
```
> If not null, it's used for fetching its model. Otherwise, it will use an associative array.

```php
protected $pdo;
```
> A PHP PDO instance.

===

#### Methods from `Piano\Mvc\DataAccessAbstract`

```php
insert(array $data, array $dataBind)
```
Create a record into database.

#### Parameters

**data**
> Array data to insert into the database table.

**dataBind**
> The bound values.

#### Return Values

> Returns the last insert id on success or FALSE on failure.

===

```php
update(array $data, $where, array $dataBind)
```
Change a database record.

#### Parameters

**data**
> Array data to insert into the database table.

**where**
> Where clause.

**dataBind**
> The bound values.

#### Return Values

> Returns TRUE on success or FALSE on failure.

===

```php
delete($where, array $dataBind = array())
```
Delete a record from database.

#### Parameters

**where**
> Where clause.

**dataBind**
> The bound values.

#### Return Values

> Returns TRUE on success or FALSE on failure.

===

```php
getAll([$configData = null, $order = null, $count = null, $offset = null])
```
Perform a query in order to return all database records.

#### Parameters

**configData**
> The query configuration.
>
> Example:
>
```php
$configData = array(
    'fetchClass' => false,
    'columns' => '*',
    'condition' => 'id = :id',
    'values' => array(
        array(':id', 1, PDO::PARAM_INT)
    )
);
```

**order**
> Like SQL ORDER.
>
> Example:
```php
'id ASC, name ASC'
```

**count**
> Integer value used to make the query return a specific set of rows.

**offset**
> Integer value used to make the query return a specific set of rows.
>
> PS: When both count and offset are used, the query to be executed has LIMIT offset, count


#### Return Values

> - When fetchClass parameter is true or omitted, it returns an array of model objects.
> - When fetchClass parameter is false, it returns an associative array.

===

```php
getFirst($configData = null, $order = null)
```
Perform a query in order to return all database records.

#### Parameters

**configData**
> The query configuration.
>
> Example:
>
```php
$configData = array(
    'fetchClass' => false,
    'columns' => '*',
    'condition' => 'id = :id',
    'values' => array(
        array(':id', 1, PDO::PARAM_INT)
    )
);
```

**order**
> Like SQL ORDER.
>
> Example:
```php
'id ASC, name ASC'
```

#### Return Values

> - When fetchClass parameter is true or omitted, it returns a model object.
> - When fetchClass parameter is false, it returns an associative array.

===

### Examples:

##### insert()
```php
$pdo = new PDO("mysql:host=host;dbname=db;", 'user', 'pass');

$userDAO = new \app\dataAccess\UserDataAccess($pdo);
$id = $userDAO->insert(
                    array(
                       'name' => ':name',
                       'email' => ':email',
                    ), array(
                       array(':name', 'John Doe', PDO::PARAM_STR),
                       array(':email', 'john@domain.com', PDO::PARAM_STR),
                    )
                );
```

##### update()
```php
$pdo = new PDO("mysql:host=host;dbname=db;", 'user', 'pass');

$userDAO = new \app\dataAccess\UserDataAccess($pdo);
$status = $userDAO->update(
                        array(
                            'name' => ':new_name',
                        ),
                        'name = :where_name',
                        array(
                           array(':new_name', 'New Name', PDO::PARAM_STR),
                           array(':where_name', 'Old Name', PDO::PARAM_STR),
                       )
                    );
```

##### delete()
```php
$pdo = new PDO("mysql:host=host;dbname=db;", 'user', 'pass');

$userDAO = new \app\dataAccess\UserDataAccess($pdo);
$status = $userDAO->delete(
                        'id = :id',
                        array(
                           array(':id', 2, PDO::PARAM_INT),
                       )
                    );

// or

$userDAO = new \app\dataAccess\UserDataAccess($pdo);
$status = $userDAO->delete('id = 2');

```

##### getAll()
```php
$pdo = new PDO("mysql:host=host;dbname=db;", 'user', 'pass');

$userDAO = new \app\dataAccess\UserDataAccess($pdo);
$users = $userDAO->getAll(
                        array(
                           'columns' => '*',
                           'condition' => 'id = :id',
                           'values' => array(
                               array(':id', 1, PDO::PARAM_INT),
                           )
                        ),
                        'id DESC',
                        10, // show 10 records
                        30 // start showing from the 30th record
                    );

// or

$userDAO = new \app\dataAccess\UserDataAccess($pdo);
$users = $userDAO->getAll();
```

##### getFirst()
```php
$pdo = new PDO("mysql:host=host;dbname=db;", 'user', 'pass');

$userDAO = new \app\dataAccess\UserDataAccess($pdo);
$user = $userDAO->getFirst(
                        array(
                           'columns' => '*',
                           'condition' => 'id = :id',
                           'values' => array(
                               array(':id', 1, PDO::PARAM_INT)
                           )
                        )
                    );

// or

$userDAO = new \app\dataAccess\UserDataAccess($pdo);
$user = $userDAO->getFirst();
```

---

## Application

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

---

## Class `Piano\Config\Ini`

The `Piano\Config\Ini` is a ini file parser. You must pass the ini path as an argument to its constructor.

##### Sample

```php
$config = new Piano\Config\Ini('/path/to/config.ini');
$configIni = $config->get();

// or

$configIni = $config->get('section_name');
```

## Class `Piano\Config\Pdo`

The `Piano\Config\Pdo` provide us an easier way to configure a `\Pdo` connection. You must pass an config array as an argument to its constructor.

##### Sample

```php
// The config array must be like this.
$config = [
    'dbAdapter' => 'mysql',
    'dbHost'    => 'localhost',
    'dbName'    => '',
    'dbUser'    => '',
    'dbPass'    => '',
];

$pdo = new Piano\Config\Pdo($config);
$pdo = $pdo->get();
```

## Class `Piano\Router`

The `Piano\Router` is a route parser. It helps you to define the application's routes.

##### Sample

```php
$router = new Piano\Router();
```

You're able to define all routes as an array or by using the `Piano\Router` object notation.

Defining routes as array.

```php
$routes = [
    'default' => [
        'route' => '/',
        'module' => 'application',
        'controller' => 'index',
        'action' => 'index',
    ],
    'contact' => [
        'route' => '/contact',
        'module' => 'application',
        'controller' => 'index',
        'action' => 'contact',
    ],
    'userEdit' => [
        'route' => '/user/:id',
        'module' => 'application',
        'controller' => 'user',
        'action' => 'edit',
        [
            ':id' => '\d+'
        ]
    ],
];

$router->setRoutes($routes);
```

Defining routes as object.

```php
$router->addRoute('default', '/', [
    'module' => 'application',
    'controller' => 'index',
    'action' => 'index',
]);

$router->addRoute('contact', '/contact', [
    'module' => 'application',
    'controller' => 'index',
    'action' => 'contact',
]);

$router->addRoute('userEdit', '/user/:id', [
    'module' => 'application',
    'controller' => 'index',
    'action' => 'contact',
    [
        ':id' => '\d+'
    ]
]);
```

You can also enable/disable SEF. *(It's useful to disable it when developing)*
It can be done by calling the method `enableSearchEngineFriendly`

```php
$router->enableSearchEngineFriendly(); // Enable
$router->enableSearchEngineFriendly(true); // Enable
$router->enableSearchEngineFriendly(false); // Disable
```

If you want to get all routes or a particular one, you could do like this:

```php
// It'll return all your routes definitions.
$router->getRoutes();

// It'll return informations from the given route, in this case `myRouteName`
$router->getRoutes('myRouteName');
```

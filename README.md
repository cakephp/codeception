CakePHP 3 Codeception Module
============================

A [codeception](http://codeception.com) module to test your CakePHP 3 powered application.

Usage
-----

```
composer require --dev cakephp/codeception:dev-master
```

Then enable it in any test suite configuration like so:

```yaml
modules:
  enabled:
    - Cake\Codeception\Helper
```

## Example Usage

```php
<?php
$I = new FunctionalTester($scenario);
$I->wantTo('ensure that adding a bookmark works');
$I->amOnPage('/bookmarks/add');
$I->see('Submit');
$I->submitForm('#add', [
    'title' => 'First bookmark',
]);
$I->seeSessionHasValues([
    'Flash'
]);
```

## Actions

### Auth

...

### Db


#### Insert record with `haveRecord($model, $data = [])`

This is useful when you need a record for just one test (temporary fixture). It
does not assert anything and returns the inserted record's ID.

```php
$I->haveRecord('users', ['email' => 'jadb@cakephp.org', 'username' => 'jadb']);
```

#### Retrieve record with `grabRecord($model, $conditions = [])`

This is a wrapper around the `Cake\ORM\Table::find('first')` method.

```php
$I->grabRecord('users', ['id' => '1']);
```

#### Assert record exists with `seeRecord($model, $conditions = [])`

This checks that the requested record does exist in the database.

```php
$I->haveRecord('users', ['username' => 'jadb']);
```

#### Assert record does not exist with `dontSeeRecord($model, $conditions = [])`

This checks that the request record does not exist in the database.

```php
$I->dontSeeRecord('users', ['email' => 'jadb@cakephp.org']);
```

### Dispatcher

...

### Miscellaneous

#### Load fixtures with `loadFixtures($fixtures[, $fixture2, ...])`

All the below forms are equivalent:

```php
$I->loadFixtures('app.posts', 'app.tags');
$I->loadFixtures(['app.posts', 'app.tags']);
$I->fixtures = ['app.posts', 'app.tags'];
```

#### Assert CakePHP version with `expectedCakePHPVersion($ver, $operator = 'ge')`

```php
$I->expectedCakePHPVersion('3.0.4');
```

### Router

#### Open page by route with `amOnRoute($route, $params = [])`

All the below forms are equivalent:

```php
$I->amOnRoute(['controller' => 'Posts', 'action' => 'add']);
$I->amOnRoute('addPost'); // assuming there is a route named `addPost`
```

#### Open page by action with `amOnAction($action, $params = [])`

All the below forms are equivalent:

```php
$I->amOnAction('Posts@add');
$I->amOnAction('Posts.add');
$I->amOnAction('PostsController@add');
$I->amOnAction('PostsController.add');
$I->amOnAction('posts@add');
$I->amOnAction('posts.add');
```

#### Assert URL matches route with `seeCurrentRouteIs($route, $params = [])`

All the below forms are equivalent:

```php
$I->seeCurrentRouteIs(['controller' => 'Posts', 'action' => 'add']);
$I->seeCurrentRouteIs('addPost'); // assuming there is a route named `addPost`
```

#### Assert URL matches action with `seeCurrentActionIs($action, $params = [])`

All the below forms are equivalent:

```php
$I->seeCurrentActionIs('Posts@add');
$I->seeCurrentActionIs('Posts.add');
$I->seeCurrentActionIs('PostsController@add');
$I->seeCurrentActionIs('PostsController.add');
$I->seeCurrentActionIs('posts@add');
$I->seeCurrentActionIs('posts.add');
```

### Session

...

### View

...

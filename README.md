CakePHP 3 Codeception Module
============================

[![Build Status](https://api.travis-ci.org/cakephp/codeception.png)](https://travis-ci.org/cakephp/codeception)
[![License](https://poser.pugx.org/cakephp/codeception/license.svg)](https://packagist.org/packages/cakephp/codeception)

A [codeception](http://codeception.com) module to test your CakePHP 3 powered application.

Usage
-----

Edit your application's [`src/Console/Installer.php`][appinstaller] file to include the installer script in
the `postInstall` method:

[appinstaller]:https://github.com/cakephp/app/blob/master/src/Console/Installer.php#L34

```php
    public static function postInstall(Event $event)
    {
        // ...
        if (class_exists('\Cake\Codeception\Console\Installer')) {
            \Cake\Codeception\Console\Installer::customizeCodeceptionBinary($event);
        }
    }
```

Now, from the command-line:

```
composer require --dev cakephp/codeception:dev-master
composer install
```

Once installed, you can now run `bootstrap` which will create all the codeception required files
in your application:

```
vendor/bin/codecept bootstrap
```

This creates the following files/folders in your `app` directory:

```
|-codeception.yml
|-src/
|---TestSuite/
|-----Codeception/
|-------AcceptanceHelper.php
|-------FunctionalHelper.php
|-------UnitHelper.php
|-tests/
|---Acceptance.suite.yml
|---Functional.suite.yml
|---Unit.suite.yml
|---Acceptance/
|-----.gitignore
|-----bootstrap.php
|-----AcceptanceTester.php
|---Fixture/
|-----dump.sql
|---Functional/
|-----.gitignore
|-----bootstrap.php
|-----FunctionalTester.php
|---Unit/
|-----.gitignore
|-----bootstrap.php
|-----UnitTester.php
```

As you might have noticed, the CakePHP implementation differs in a couple things:

- uses CamelCase suite names (`Functional` vs. `functional`)
- uses `bootstrap.php`, no underscore prefix (vs. `_bootstrap.php`)
- uses `src/TestSuite/Codeception` for custom modules (helpers) (vs. `tests/_helpers`)
- uses `tmp/tests` to store logs (vs. `tests/_logs`)
- adds a `.gitignore` to never track auto-generated files
- adds custom templates for various generated files using the `codecept` binary

To better understand how Codeception tests work, please check the [official documentation][codeception_docs].

[codeception_docs]:http://codeception.com/docs/01-Introduction

## Example Cept

```php
<?php
$I = new FunctionalTester($scenario);
$I->wantTo('ensure that adding a bookmark works');
$I->amOnPage('/bookmarks/add');
$I->see('Submit');
$I->submitForm('#add', [
    'title' => 'First bookmark',
]);
$I->seeInSession([
    'Flash'
]);
```

## Actions

### Auth

...

### Config

#### Assert config key(/value) with `seeInConfig($key, $value = null)`

```php
$I->seeInConfig('App.name'); // checks only that the key exists
$I->seeInConfig('App.name', 'CakePHP');
$I->seeInConfig(['App.name' => 'CakePHP']);
```

#### Assert no config key(/value) with `dontSeeInConfig($key, $value = null)`

```php
$I->dontSeeInConfig('App.name'); // checks only that the key does not exist
$I->dontSeeInConfig('App.name', 'CakePHP');
$I->dontSeeInConfig(['App.name' => 'CakePHP']);
```

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
$I->seeRecord('users', ['username' => 'jadb']);
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

#### Insert key/value(s) in session with `haveInSession($key, $value = null)`

```php
$I->haveInSession('redirect', Router::url(['_name' => 'dashboard']));
$I->haveInSession(['redirect' => Router::url(['_name' => 'dashboard'])]);
```

#### Assert key(/value) in session with `seeInSession($key, $value = null)`

```php
$I->seeInSession('redirect'); // only checks the key exists.
$I->seeInSession('redirect', Router::url(['_name' => 'dashboard']));
$I->seeInSession(['redirect', Router::url(['_name' => 'dashboard'])]);
```

#### Assert key(/value) not in session with `dontSeeInSession($key, $value = null)`

```php
$I->dontSeeInSession('redirect'); // only checks the key does not exist.
$I->dontSeeInSession('redirect', Router::url(['_name' => 'dashboard']));
$I->dontSeeInSession(['redirect', Router::url(['_name' => 'dashboard'])]);
```


### View

...

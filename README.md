CakePHP 3 Codeception Module
============================

[![Build Status](https://api.travis-ci.org/cakephp/codeception.png)](https://travis-ci.org/cakephp/codeception)
[![License](https://poser.pugx.org/cakephp/codeception/license.svg)](https://packagist.org/packages/cakephp/codeception)

A [codeception](http://codeception.com) module to test your CakePHP 3 powered application. Using Codeception with CakePHP opens up a whole new set of testing capabilities.

##### Front-end testing

_(i.e. browser-based workflow tests)_

  - [[Functional Tests]](http://codeception.com/docs/05-FunctionalTests)
    - does the App run the way we expect
  - [[Acceptance Tests]](http://codeception.com/docs/04-AcceptanceTests)
    - does the App do what the customer/user expects

##### Back-end testing

_(i.e. direct, internal method tests)_

- [[API Tests]](http://codeception.com/docs/10-WebServices)
  - does the App's API work
- [[Unit Tests]](http://codeception.com/docs/06-UnitTests)
  - do the App's internal functions do what we expect

Usage
-----

From a CakePHP application, run the following from the command-line:

```console
$ composer require --dev cakephp/codeception:dev-master && composer run-script post-install-cmd
```

If you are developing a plugin, add the post-install script to your `composer.json` first:

```json
{
    "scripts": {
        "post-install-cmd": "Cake\\Codeception\\Console\\Installer::customizeCodeceptionBinary"
    }
}
```

Once installed, you can now run `bootstrap` which will create all the codeception required files
in your application:

```console
$ vendor/bin/codecept bootstrap
```

This creates the following files/folders in your `app` directory:

```
├── codeception.yml
├── src
│   └── TestSuite
│       └── Codeception
|           ├── AcceptanceTester.php
|           ├── FunctionalTester.php
|           ├── UnitTester.php
|           ├── Helper
│           │   ├── Acceptance.php
│           │   ├── Functional.php
│           │   └── Unit.php
|           └── _generated
|               └── .gitignore
└── tests
    ├── Acceptance.suite.yml
    ├── Functional.suite.yml
    ├── Unit.suite.yml
    ├── Acceptance
    │   └── bootstrap.php
    ├── Fixture
    │   └── dump.sql
    ├── Functional
    │   └── bootstrap.php
    └── Unit
        └── bootstrap.php
```

As you might have noticed, the CakePHP implementation differs in a couple things:

- uses CamelCase suite names (`Functional` vs. `functional`)
- uses `bootstrap.php`, no underscore prefix (vs. `_bootstrap.php`)
- uses `src/TestSuite/Codeception` for custom modules (helpers) (vs. `tests/_helpers`)
- uses `tmp/tests` to store logs (vs. `tests/_logs`)
- uses `tests/Fixture` to fixture data (vs. `tests/_data`)
- uses `tests/Envs` to fixture data (vs. `tests/_envs`)
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

#### Load fixtures

In your `Cest` test case, write `$fixutures` property:

```php
class AwesomeCest
{
    public $fixtures = [
        'app.users',
        'app.posts',
    ];

    // ...
}
```

You can use `$autoFixtures`, `$dropTables` property, and `loadFixtures()` method:

```php
class AwesomeCest
{
    public $autoFixtures = false;
    public $dropTables = false;
    public $fixtures = [
        'app.users',
        'app.posts',
    ];

    public function tryYourSenario($I)
    {
        // load fixtures manually
        $I->loadFixtures('Users', 'Posts');
        // or load all fixtures
        $I->loadFixtures();
        // ...
    }
}
```

In your `Cept` test case, use `$I->useFixtures()` and `$I->loadFixtures()`:

```php
$I = new FunctionalTester($scenario);

// You should call `useFixtures` before `loadFixtures`
$I->useFixtures('app.users', 'app.posts');
// Then load fixtures manually
$I->loadFixtures('Users', 'Posts');
// or load all fixtures
$I->loadFixtures();
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

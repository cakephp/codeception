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

...

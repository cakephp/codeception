<?php use App\TestSuite\Codeception\FunctionalTester;
$I = new FunctionalTester($scenario);
$I->wantTo('view the cookies index page');
$I->amOnRoute(['controller' => 'Cookies', 'action' => 'index']);
$I->seeCurrentActionIs('cookies.index');
$I->seeResponseCodeIs(200);
$I->see('The cookies page.');
$I->seeCookie('foo');

<?php use App\Test\Functional\FunctionalTester;
$I = new FunctionalTester($scenario);
$I->wantTo('check the homepage');
$I->amOnPage('/');
$I->see('CakePHP: the rapid development php framework');

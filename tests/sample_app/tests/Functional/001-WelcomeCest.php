<?php
namespace App\Test\Functional;

use App\TestSuite\Codeception\FunctionalTester;

class WelcomeCest
{
    // tests
    public function checkTheHomepage(FunctionalTester $I)
    {
        $I->amOnPage('/');
        $I->see('CakePHP: the rapid development php framework');
    }
}

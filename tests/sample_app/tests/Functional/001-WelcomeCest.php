<?php
namespace App\Test\Functional;

use App\Test\Functional\FunctionalTester;

class WelcomeCest
{
    // tests
    public function checkTheHomepage(FunctionalTester $I)
    {
        $I->amOnPage('/');
        $I->see('CakePHP: the rapid development php framework');
    }
}

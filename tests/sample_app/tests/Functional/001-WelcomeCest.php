<?php
use \FunctionalTester;

class WelcomeCest
{
    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }

    // tests
    public function checkTheHomepage(FunctionalTester $I)
    {
        $I->amOnPage('/');
        $I->see('CakePHP: the rapid development php framework');
    }
}

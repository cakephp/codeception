<?php

namespace App\Test\Functional;

use App\TestSuite\Codeception\FunctionalTester;

/**
 * Test for loadFixtures()
 */
class FixtureLoadCest
{

    public $autoFixtures = false;
    public $fixtures = [
        'core.Authors',
    ];

    /**
     * @param FunctionalTester $I
     */
    public function tryLoadFixtures(FunctionalTester $I)
    {
        $I->wantTo('loading the Authors fixture');
        $I->loadFixtures('Authors');
        $I->seeInDatabase('authors', ['id' => 1, 'name' => 'mariano']);
    }

}

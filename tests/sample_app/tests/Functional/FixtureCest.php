<?php

namespace App\Test\Functional;

use App\TestSuite\Codeception\FunctionalTester;

/**
 * Test for auto load fixtures.
 */
class FixtureCest
{

    public $fixtures = [
        'core.Authors',
    ];

    /**
     * @param FunctionalTester $I
     */
    public function tryAutoLoadFixtures(FunctionalTester $I)
    {
        $I->wantTo('auto load the Authors fixture');
        $I->seeInDatabase('authors', ['id' => 1, 'name' => 'mariano']);
    }

}

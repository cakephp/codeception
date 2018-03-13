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
        'core.authors',
        'core.posts',
    ];

    /**
     * @param FunctionalTester $I
     */
    public function tryLoadFixtures(FunctionalTester $I)
    {
        $I->wantTo('loading fixtures manulary');
        $I->loadFixtures('Authors');
        $I->seeInDatabase('authors', ['id' => 1, 'name' => 'mariano']);

        $I->loadFixtures();
        $I->seeInDatabase('posts', ['author_id' => 1, 'title' => 'First Post']);
    }

}

<?php

namespace App\Test\Functional;

use App\TestSuite\Codeception\FunctionalTester;

/**
 * Test for CakeFixture::loadFixtures()
 */
class LoadFixturesCest
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

        // useFixtures use only once
        $I->expectException('\Codeception\Exception\ModuleException', function () use ($I) {
            $I->useFixtures('core.tags');
        });
    }
}

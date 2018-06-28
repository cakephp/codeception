<?php

namespace App\Test\Unit\Helper;

use App\TestSuite\Codeception\UnitTester;

/**
 * Test for CakeFixture module, auto load fixtures.
 */
class DbHelperCest
{

    public $fixtures = [
        'app.Projects',
        'app.Tasks',
    ];

    /**
     * @param UnitTester $I
     */
    public function testCleanUpInsertedRecordsWithForeignKey(UnitTester $I)
    {
        $I->wantTo('append and cleanup records');

        // Tasks depends Projects
        $projectId = $I->haveRecord('Projects', ['name' => 'Awesome Project']);
        $I->haveRecord('Tasks', ['title' => 'Awesome Task', 'project_id' => $projectId]);

        // Cleanup Projects before Tasks, it will be throw exception if can't drop constraints.
        $I->cleanUpInsertedRecords('Projects');
        $I->cleanUpInsertedRecords('Tasks');
    }
}

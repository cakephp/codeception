<?php
namespace Cake\Codeception\Test\TestCase;

use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase
{
    public function testCodeceptProperlyOverwritten()
    {
        $result = file_get_contents(TEST_APP_BIN . 'codecept');

        $expectedBootstrap = 'new Cake\Codeception\Command\Bootstrap';
        $this->assertContains($expectedBootstrap, $result, 'Codecept must use cake build command');

        $expectedGenerateCest = 'new Cake\Codeception\Command\GenerateCest';
        $this->assertContains($expectedGenerateCest, $result, 'Codecept must use cake generate:cest command');

        $expectedGenerateSuite = 'new Cake\Codeception\Command\GenerateSuite';
        $this->assertContains($expectedGenerateSuite, $result, 'Codecept must use cake generate:suite command');
    }
}

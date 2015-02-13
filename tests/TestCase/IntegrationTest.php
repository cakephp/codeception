<?php
namespace Cake\Codeception\Test\TestCase;

use PHPUnit_Framework_TestCase as TestCase;

class IntegrationTest extends TestCase
{
    public function testCodeceptProperlyOverwritten()
    {
        $result = file_get_contents(TEST_APP_BIN . 'codecept');

        $expected = 'new Cake\Codeception\Command\Build';
        $this->assertContains($expected, $result, 'Codecept must use cake build command');

        $expected = 'new Cake\Codeception\Command\Bootstrap';
        $this->assertContains($expected, $result, 'Codecept must use cake build command');
    }
}

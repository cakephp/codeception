<?php
namespace Cake\Codeception\Test\TestCase;

use PHPUnit_Framework_TestCase as TestCase;

class IntegrationTest extends TestCase
{
    public function testCodeceptProperlyOverwritten()
    {
        $result = file_get_contents(dirname(__DIR__) . '/test_app/vendor/bin/codecept');

        $expected = 'new Cake\Codeception\Command\Build';
        $this->assertContains($expected, $result, 'Codecept must use cake build command');

        $expected = 'new Cake\Codeception\Command\Bootstrap';
        $this->assertContains($expected, $result, 'Codecept must use cake build command');
    }
}

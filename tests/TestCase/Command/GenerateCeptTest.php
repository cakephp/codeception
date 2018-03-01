<?php
namespace Cake\Codeception\Test\TestCase\Command;

use PHPUnit\Framework\TestCase;

class GenerateCeptTest extends TestCase
{
    public function testExecute()
    {
        $file = TEST_APP_ROOT . 'tests' . DS . 'Functional' . DS . 'FooCept.php';
        $this->assertTrue(file_exists($file), "Cest file [$file] should be generated");
    }

    public function testGeneratorProduce()
    {
        $result = file_get_contents(TEST_APP_ROOT . 'tests' . DS . 'Functional' . DS . 'FooCept.php');

        $expected = 'use App\TestSuite\Codeception\FunctionalTester;';
        $this->assertContains($expected, $result, 'Using tester class must be including namespace');
    }
}

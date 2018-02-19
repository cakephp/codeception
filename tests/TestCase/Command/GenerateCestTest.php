<?php
namespace Cake\Codeception\Test\TestCase\Command;

use PHPUnit\Framework\TestCase;

class GenerateCestTest extends TestCase
{
    public function testExecute()
    {
        $file = TEST_APP_ROOT . 'tests' . DS . 'Functional' . DS . 'FooCest.php';
        $this->assertTrue(file_exists($file), "Cest file [$file] should be generated");
    }

    public function testGeneratorProduce()
    {
        $result = file_get_contents(TEST_APP_ROOT . 'tests' . DS . 'Functional' . DS . 'FooCest.php');

        $expected = 'namespace App\Test\Functional;';
        $this->assertContains($expected, $result, 'Namespace should be added');

        $expected = "\n" .
            '    // @codingStandardsIgnoreStart' . "\n" .
            '    public function _before(FunctionalTester $I)// @codingStandardsIgnoreEnd';
        $this->assertContains($expected, $result, 'Coding standards should be ignored');

        $expected = "\n" .
            '    // @codingStandardsIgnoreStart' . "\n" .
            '    public function _after(FunctionalTester $I)// @codingStandardsIgnoreEnd';
        $this->assertContains($expected, $result, 'Coding standards should be ignored');
    }
}

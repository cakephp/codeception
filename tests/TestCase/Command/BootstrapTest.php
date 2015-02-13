<?php
namespace Cake\Codeception\Test\TestCase\Command;

use PHPUnit_Framework_TestCase as TestCase;

class BootstrapTest extends TestCase
{

    public function testHelpersCreatedInTestSuite()
    {
        $testSuiteDir = TEST_APP_ROOT . 'src' . DS . 'TestSuite' . DS . 'Codeception' . DS;
        $this->assertTrue(is_dir($testSuiteDir), 'src/TestSuite/Codeception directory must be auto-created');
        // @codingStandardsIgnoreStart
        $this->assertTrue(file_exists($testSuiteDir . 'AcceptanceHelper.php'), 'AcceptanceHelper must be auto-created');
        $this->assertTrue(file_exists($testSuiteDir . 'FunctionalHelper.php'), 'FunctionalHelper must be auto-created');
        // @codingStandardsIgnoreEnd
        $this->assertTrue(file_exists($testSuiteDir . 'UnitHelper.php'), 'UnitHelper must be auto-created');
    }

    public function testSuitesCreatedInTests()
    {
        $testsDir = TEST_APP_ROOT . 'tests' . DS;

        $acceptanceDir = $testsDir . 'Acceptance' . DS;
        $this->assertTrue(is_dir($acceptanceDir), 'Acceptance test suite directory must be auto-created');
        // @codingStandardsIgnoreStart
        $this->assertTrue(file_exists($acceptanceDir . 'AcceptanceTester.php'), 'AcceptanceTester must be auto-generated');
        $this->assertTrue(file_exists($acceptanceDir . 'bootstrap.php'), 'Acceptance bootstrap.php must be auto-created');
        // @codingStandardsIgnoreEnd
        $this->assertTrue(file_exists($acceptanceDir . '.gitignore'), 'Acceptance .gitignore must be auto-created');

        $functionalDir = $testsDir . 'Functional' . DS;
        $this->assertTrue(is_dir($functionalDir), 'Functional test suite directory must be auto-created');
        // @codingStandardsIgnoreStart
        $this->assertTrue(file_exists($functionalDir . 'FunctionalTester.php'), 'FunctionalTester must be auto-generated');
        $this->assertTrue(file_exists($functionalDir . 'bootstrap.php'), 'Functional bootstrap.php must be auto-created');
        // @codingStandardsIgnoreEnd
        $this->assertTrue(file_exists($functionalDir . '.gitignore'), 'Functional .gitignore must be auto-created');

        $unitDir = $testsDir . 'Unit' . DS;
        $this->assertTrue(is_dir($unitDir), 'Unit test suite directory must be auto-created');
        $this->assertTrue(file_exists($unitDir . 'UnitTester.php'), 'UnitTester must be auto-generated');
        $this->assertTrue(file_exists($unitDir . 'bootstrap.php'), 'Unit bootstrap.php must be auto-created');
        $this->assertTrue(file_exists($unitDir . '.gitignore'), 'Unit .gitignore must be auto-created');
    }

    public function testCoreConfigurationFileCreated()
    {
        $configFilePath = TEST_APP_ROOT . 'codeception.yml';
        $this->assertTrue(file_exists($configFilePath), 'File `codeception.yml` must be auto-created');

        $result = file_get_contents($configFilePath);
        $this->assertNotContains('dsn', $result, 'No dsn configuration should be included');
    }

    public function testAcceptanceConfigurationFileCreated()
    {
        $configFilePath = TEST_APP_ROOT . 'tests' . DS . 'Acceptance.suite.yml';
        $this->assertTrue(file_exists($configFilePath), 'File `acceptance.suite.yml` must be auto-created');

        $result = file_get_contents($configFilePath);
        $this->assertContains('Cake\Codeception\Helper', $result, 'Cake helper module must be enabled');
    }

    public function testFunctionalConfigurationFileCreated()
    {
        $configFilePath = TEST_APP_ROOT . 'tests' . DS . 'Functional.suite.yml';
        $this->assertTrue(file_exists($configFilePath), 'File `functional.suite.yml` must be auto-created');

        $result = file_get_contents($configFilePath);
        $this->assertContains('Cake\Codeception\Helper', $result, 'Cake helper module must be enabled');
    }

    public function testUnitConfigurationFileCreated()
    {
        $configFilePath = TEST_APP_ROOT . 'tests' . DS . 'Unit.suite.yml';
        $this->assertTrue(file_exists($configFilePath), 'File `unit.suite.yml` must be auto-created');

        $result = file_get_contents($configFilePath);
        $this->assertNotContains('Cake\Codeception\Helper', $result, 'Cake helper module must not be enabled');
    }
}

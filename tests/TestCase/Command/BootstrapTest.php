<?php
namespace Cake\Codeception\Test\TestCase\Command;

use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase
{

    public function testHelpersCreatedInTestSuite()
    {
        $testSuiteDir = TEST_APP_ROOT . 'src' . DS . 'TestSuite' . DS . 'Codeception' . DS;
        $this->assertTrue(is_dir($testSuiteDir), 'src/TestSuite/Codeception directory must be auto-created');

        $this->assertFileExists($testSuiteDir . 'Helper/Acceptance.php', 'AcceptanceHelper must be auto-created');
        $this->assertFileExists($testSuiteDir . 'Helper/Functional.php', 'FunctionalHelper must be auto-created');
        $this->assertFileExists($testSuiteDir . 'Helper/Unit.php', 'UnitHelper must be auto-created');

        $this->assertFileExists($testSuiteDir . 'AcceptanceTester.php', 'AcceptanceTester must be auto-generated');
        $this->assertFileExists($testSuiteDir . 'FunctionalTester.php', 'FunctionalTester must be auto-generated');
        $this->assertFileExists($testSuiteDir . 'UnitTester.php', 'UnitTester must be auto-generated');

        // @codingStandardsIgnoreStart
        $this->assertFileExists($testSuiteDir . '_generated/.gitignore', '_generated directory .gitignore must be auto-created');
        // @codingStandardsIgnoreEnd
    }

    public function testSuitesCreatedInTests()
    {
        $testsDir = TEST_APP_ROOT . 'tests' . DS;

        $acceptanceDir = $testsDir . 'Acceptance' . DS;
        $this->assertTrue(is_dir($acceptanceDir), 'Acceptance test suite directory must be auto-created');
        $this->assertFileExists($acceptanceDir . 'bootstrap.php', 'Acceptance bootstrap.php must be auto-created');
        
        $functionalDir = $testsDir . 'Functional' . DS;
        $this->assertTrue(is_dir($functionalDir), 'Functional test suite directory must be auto-created');
        $this->assertFileExists($functionalDir . 'bootstrap.php', 'Functional bootstrap.php must be auto-created');

        $unitDir = $testsDir . 'Unit' . DS;
        $this->assertTrue(is_dir($unitDir), 'Unit test suite directory must be auto-created');
        $this->assertFileExists($unitDir . 'bootstrap.php', 'Unit bootstrap.php must be auto-created');
    }

    public function testCoreConfigurationFileCreated()
    {
        $configFilePath = TEST_APP_ROOT . 'codeception.yml';
        $this->assertTrue(file_exists($configFilePath), 'File `codeception.yml` must be auto-created');

        $result = file_get_contents($configFilePath);
        $this->assertNotContains('dsn', $result, 'No dsn configuration should be included');
        $this->assertContains('namespace: App\TestSuite\Codeception', $result, 'namespace must be enabled');
        $this->assertContains('bootstrap: bootstrap.php', $result, 'bootstrap must be enabled');
    }

    public function testAcceptanceConfigurationFileCreated()
    {
        $configFilePath = TEST_APP_ROOT . 'tests' . DS . 'Acceptance.suite.yml';
        $this->assertTrue(file_exists($configFilePath), 'File `acceptance.suite.yml` must be auto-created');

        $result = file_get_contents($configFilePath);
        $this->assertContains('namespace: App\TestSuite\Codeception', $result, 'namespace must be enabled');
        $this->assertContains('suite_namespace: App\Test\Acceptance', $result, 'suite_namespace must be enabled');
        $this->assertNotContains('\Cake\Codeception', $result, 'Cake helper module must not be enabled');
        // @codingStandardsIgnoreStart
        $this->assertContains('\App\TestSuite\Codeception\Helper\Acceptance', $result, 'Acceptance helper must be enabled');
        // @codingStandardsIgnoreEnd
    }

    public function testFunctionalConfigurationFileCreated()
    {
        $configFilePath = TEST_APP_ROOT . 'tests' . DS . 'Functional.suite.yml';
        $this->assertTrue(file_exists($configFilePath), 'File `functional.suite.yml` must be auto-created');

        $result = file_get_contents($configFilePath);
        $this->assertContains('namespace: App\TestSuite\Codeception', $result, 'namespace must be enabled');
        $this->assertContains('suite_namespace: App\Test\Functional', $result, 'suite_namespace must be enabled');
        $this->assertContains('\Cake\Codeception\Helper', $result, 'Cake helper module must be enabled');
        // @codingStandardsIgnoreStart
        $this->assertContains('\App\TestSuite\Codeception\Helper\Functional', $result, 'Functional helper must be enabled');
        // @codingStandardsIgnoreEnd
    }

    public function testUnitConfigurationFileCreated()
    {
        $configFilePath = TEST_APP_ROOT . 'tests' . DS . 'Unit.suite.yml';
        $this->assertTrue(file_exists($configFilePath), 'File `unit.suite.yml` must be auto-created');

        $result = file_get_contents($configFilePath);
        $this->assertContains('namespace: App\TestSuite\Codeception', $result, 'namespace must be enabled');
        $this->assertContains('suite_namespace: App\Test\Unit', $result, 'suite_namespace must be enabled');
        $this->assertNotContains('\Cake\Codeception\Helper', $result, 'Cake helper module must not be enabled');
        $this->assertContains('\Cake\Codeception\Framework', $result, 'Cake framework module must be enabled');
        $this->assertContains('\App\TestSuite\Codeception\Helper\Unit', $result, 'Unit helper must be enabled');
    }
}

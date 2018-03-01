<?php
namespace Cake\Codeception\Test\TestCase\Command;

use PHPUnit\Framework\TestCase;

class GenerateSuiteTest extends TestCase
{

    public function testHelpersCreatedInTestSuite()
    {
        $testSuiteDir = TEST_APP_ROOT . 'src' . DS . 'TestSuite' . DS . 'Codeception' . DS;

        $this->assertTrue(file_exists($testSuiteDir . 'Helper/Api.php'), 'Api helper must be auto-created');
        $this->assertTrue(file_exists($testSuiteDir . 'ApiTester.php'), 'ApiTester must be auto-generated');
    }

    public function testSuitesCreatedInTests()
    {
        $testsDir = TEST_APP_ROOT . 'tests' . DS . 'Api' . DS;

        $this->assertTrue(is_dir($testsDir), 'Api test suite directory must be auto-created');
        $this->assertFileExists($testsDir . 'bootstrap.php', 'Api bootstrap.php must be auto-created');
    }

    public function testApiConfigurationFileCreated()
    {
        $configFilePath = TEST_APP_ROOT . 'tests' . DS . 'Api.suite.yml';
        $this->assertTrue(file_exists($configFilePath), 'File `Api.suite.yml` must be auto-created');

        $result = file_get_contents($configFilePath);
        $this->assertContains('namespace: App\TestSuite\Codeception', $result, 'namespace must be enabled');
        $this->assertContains('suite_namespace: App\Test\Api', $result, 'suite_namespace must be enabled');
        $this->assertContains('\Cake\Codeception\Helper', $result, 'Cake helper module must be enabled');
        $this->assertContains('\App\TestSuite\Codeception\Helper\Api', $result, 'Api helper must be enabled');
    }
}

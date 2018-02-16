<?php

namespace Cake\Codeception\Template;

use Codeception\Template\Bootstrap as BaseTemplate;
use Symfony\Component\Yaml\Yaml;

class Bootstrap extends BaseTemplate
{
    // defaults
    protected $baseNamespace = 'App\\';
    protected $supportDir = 'src/TestSuite/Codeception';
    protected $outputDir = 'tmp/tests';
    protected $dataDir = 'tests/Fixture';
    protected $envsDir = 'tests/Envs';

    public function setup()
    {
        // Rewrite namespace
        $input = $this->input;
        if ($input->getOption('namespace')) {
            $this->baseNamespace = trim($input->getOption('namespace'), '\\') . '\\';
        }
        $input->setOption('namespace', $this->baseNamespace . 'TestSuite\Codeception\\');

        parent::setup();
    }

    protected function createDirs()
    {
         $this->createDirectoryFor('tests');
         $this->createEmptyDirectory($this->outputDir);
         $this->createEmptyDirectory($this->dataDir);
         $this->createDirectoryFor($this->supportDir . DIRECTORY_SEPARATOR . '_generated');
         $this->createDirectoryFor($this->supportDir . DIRECTORY_SEPARATOR . 'Helper');
         $this->gitIgnore($this->outputDir);
         $this->gitIgnore($this->supportDir . '/_generated');
    }

    public function createGlobalConfig()
    {
        $basicConfig = [
            'paths'    => [
                'tests'   => 'tests',
                'output'  => $this->outputDir,
                'data'    => $this->dataDir,
                'support' => $this->supportDir,
                'envs'    => $this->envsDir,
            ],
            'settings' => [
                'bootstrap'    => 'bootstrap.php',
                'colors'       => (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN'),
                'memory_limit' => '1024M'
            ],
            'actor_suffix' => 'Tester',
            'extensions' => [
                'enabled' => ['Codeception\Extension\RunFailed']
            ]
        ];

        $str = Yaml::dump($basicConfig, 4);
        if ($this->namespace) {
            $namespace = rtrim($this->namespace, '\\');
            $str = "namespace: $namespace\n" . $str;
        }
        $this->createFile('codeception.yml', $str);
    }

    protected function createSuiteBootsrap($suite)
    {
        $content = <<<EOF
<?php
// Here you can initialize variables that will be available to your tests, e.g. the app's bootstrap.php
EOF;
        $file = implode(DIRECTORY_SEPARATOR, ['tests', $suite, 'bootstrap.php']);
        $this->createFile($file, $content);
    }

    protected function createFunctionalSuite($actor = 'Functional')
    {
        $suiteConfig = <<<EOF
# Codeception Test Suite Configuration
#
# Suite for functional tests
# Emulate web requests and make application process them
# Include one of framework modules (Symfony2, Yii2, Laravel5) to use it
# Remove this suite if you don't use frameworks

actor: $actor{$this->actorSuffix}
namespace: {$this->baseNamespace}TestSuite\Codeception
suite_namespace: {$this->baseNamespace}Test\Functional
modules:
    enabled:
        # add a framework module here
        - \Cake\Codeception\Helper
        - \\{$this->namespace}Helper\Functional
EOF;
        $this->createSuite('Functional', $actor, $suiteConfig);
        $this->createSuiteBootsrap('Functional');
    }

    protected function createAcceptanceSuite($actor = 'Acceptance')
    {
        $suiteConfig = <<<EOF
# Codeception Test Suite Configuration
#
# Suite for acceptance tests.
# Perform tests in browser using the WebDriver or PhpBrowser.
# If you need both WebDriver and PHPBrowser tests - create a separate suite.

actor: $actor{$this->actorSuffix}
namespace: {$this->baseNamespace}TestSuite\Codeception
suite_namespace: {$this->baseNamespace}Test\Acceptance
modules:
    enabled:
        - PhpBrowser:
            url: http://localhost/myapp
        - \\{$this->namespace}Helper\Acceptance
EOF;
        $this->createSuite('Acceptance', $actor, $suiteConfig);
        $this->createSuiteBootsrap('Acceptance');
    }

    protected function createUnitSuite($actor = 'Unit')
    {
        $suiteConfig = <<<EOF
# Codeception Test Suite Configuration
#
# Suite for unit or integration tests.

actor: $actor{$this->actorSuffix}
namespace: {$this->baseNamespace}TestSuite\Codeception
suite_namespace: {$this->baseNamespace}Test\Unit
modules:
    enabled:
        - Asserts
        - \Cake\Codeception\Framework
        - \\{$this->namespace}Helper\Unit
EOF;
        $this->createSuite('Unit', $actor, $suiteConfig);
        $this->createSuiteBootsrap('Unit');
    }
}

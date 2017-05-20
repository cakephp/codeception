<?php
namespace Cake\Codeception\Command;

use Cake\Codeception\Lib\Generator\Helper;
use Cake\Shell\ServerShell;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class Bootstrap extends \Codeception\Command\Bootstrap
{
    protected $namespace = 'App\\';
    protected $actorSuffix = 'Tester';
    protected $supportDir = 'src/TestSuite/Codeception';
    protected $logDir = 'tmp/tests';
    protected $dataDir = 'tests/Fixture';

    /**
     * Executes the `codecept bootstrap` command.
     *
     * @param InputInterface $input Input.
     * @param OutputInterface $output Output.
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('namespace')) {
            $this->namespace = trim($input->getOption('namespace'), '\\') . '\\';
        }

        if ($input->getOption('actor')) {
            $this->actorSuffix = $input->getOption('actor');
        }

        $path = $input->getArgument('path');

        if (!is_dir($path)) {
            $output->writeln("<error>\nDirectory '$path' does not exist\n</error>");
            return;
        }

        $realpath = realpath($path);
        chdir($path);
        @mkdir('src/TestSuite');

        if (file_exists('codeception.yml')) {
            $output->writeln("<error>\nProject is already initialized in '$path'\n</error>");
            return;
        }

        $output->writeln(
            "<fg=white;bg=magenta>Initializing Codeception in " . $realpath . "</fg=white;bg=magenta>\n"
        );

        $this->createGlobalConfig();
        $output->writeln("File codeception.yml created       <- global configuration");

        $this->createDirs();

        if (!$input->getOption('empty')) {
            $this->createUnitSuite();
            $output->writeln("tests/unit created                 <- unit tests");
            $output->writeln("tests/unit.suite.yml written       <- unit tests suite configuration");
            $this->createFunctionalSuite();
            $output->writeln("tests/functional created           <- functional tests");
            $output->writeln("tests/functional.suite.yml written <- functional tests suite configuration");
            $this->createAcceptanceSuite();
            $output->writeln("tests/acceptance created           <- acceptance tests");
            $output->writeln("tests/acceptance.suite.yml written <- acceptance tests suite configuration");
        }

        $output->writeln(" --- ");
        $this->ignoreFolderContent('tmp/tests');
        if (file_exists('.gitignore')) {
            file_put_contents('.gitignore', file_get_contents('.gitignore') . "\ntmp/tests/*");
            $output->writeln("tmp/tests was added to .gitignore");
        }

        file_put_contents('tests/bootstrap.php', "<?php\n// This is global bootstrap for autoloading\n");
        $output->writeln("tests/bootstrap.php written <- global bootstrap file");

        $output->writeln("<info>Building initial {$this->actorSuffix} classes</info>");
        $this->getApplication()->find('build')->run(
            new ArrayInput(['command' => 'build']),
            $output
        );

        $output->writeln("<info>\nBootstrap is done. Check out " . $realpath . "/tests directory</info>");
    }

    /**
     * Creates the `codeception.yml` file.
     *
     * @return void
     */
    public function createGlobalConfig()
    {
        $basicConfig = [
            'actor' => $this->actorSuffix,
            'paths'    => [
                'tests'   => 'tests',
                'log'     => $this->logDir,
                'data'    => $this->dataDir,
                'support' => $this->supportDir,
                'envs'    => $this->envsDir,
            ],
            'settings' => [
                'bootstrap'    => 'bootstrap.php',
                'colors'       => (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN'),
                'memory_limit' => '1024M'
            ],
            'extensions' => [
                'enabled' => ['Codeception\Extension\RunFailed']
            ]
        ];

        $str = Yaml::dump($basicConfig, 4);
        if ($this->namespace) {
            $namespace = rtrim($this->namespace, '\\');
            $str = "namespace: $namespace\n" . $str;
        }
        file_put_contents('codeception.yml', $str);
    }

    /**
     * Creates the functional suite's test directory and configuration file.
     *
     * @param string $actor Name of the actor to use (i.e. FunctionalTester class).
     */
    protected function createFunctionalSuite($actor = 'Functional')
    {
        $suiteConfig = <<<EOF
# CakePHP Codeception Functional Test Suite Configuration
#
# Suite for functional (integration) tests
# Emulate web requests and make application process them
# Include one of framework modules (Symfony2, Yii2, Laravel5) to use it

class_name: $actor{$this->actorSuffix}
namespace: {$this->namespace}Test\\Functional
modules:
    enabled:
        # add framework module here
        - \\Cake\\Codeception\\Helper
        - \\{$this->namespace}TestSuite\\Codeception\\{$actor}Helper
EOF;
        $this->createSuite('Functional', $actor, $suiteConfig);
    }

    /**
     * Creates the acceptance suite's test directory and configuration file.
     *
     * @param string $actor Name of the actor to use (i.e. AcceptancTester class).
     */
    protected function createAcceptanceSuite($actor = 'Acceptance')
    {
        $url = 'http://' . ServerShell::DEFAULT_HOST . ':' . ServerShell::DEFAULT_PORT;
        $suiteConfig = <<<EOF
# CakePHP Codeception Acceptance Test Suite Configuration
#
# Suite for acceptance tests.
# Perform tests in browser using the WebDriver or PhpBrowser.
# If you need both WebDriver and PHPBrowser tests - create a separate suite.

class_name: $actor{$this->actorSuffix}
namespace: {$this->namespace}Test\\Acceptance
modules:
    enabled:
        - PhpBrowser:
            url: {$url}
        - \\{$this->namespace}TestSuite\\Codeception\\{$actor}Helper
EOF;
        $this->createSuite('Acceptance', $actor, $suiteConfig);
    }

    /**
     * Creates the unit suite's test directory and configuration file.
     *
     * @param string $actor Name of the actor to use (i.e. UnitTester class).
     */
    protected function createUnitSuite($actor = 'Unit')
    {
        $suiteConfig = <<<EOF
# CakePHP Codeception Unit Test Suite Configuration
#
# Suite for unit (internal) tests.

class_name: $actor{$this->actorSuffix}
namespace: {$this->namespace}Test\\Unit
modules:
    enabled:
        - \\Cake\\Codeception\\Framework
        - Asserts
        - \\{$this->namespace}TestSuite\\Codeception\\{$actor}Helper
EOF;
        $this->createSuite('Unit', $actor, $suiteConfig);
    }

    /**
     * Creates the common test suites artifacts.
     *
     * @param string $suite Name of the suite.
     * @param string $actor Name of the actor to use (*Tester class).
     * @param string $config YAML configuration.
     */
    protected function createSuite($suite, $actor, $config)
    {
        @mkdir("tests/$suite");
        file_put_contents(
            "tests/$suite/.gitignore",
            $actor . 'Tester.php'
        );
        $this->ignoreFolderContent("tests/$suite/_generated");
        file_put_contents(
            "tests/$suite/bootstrap.php",
            <<<'EOF'
<?php
// Here you can initialize variables that will be available to your tests, e.g. the app's bootstrap.php
$findRoot = function ($root) {
    do {
        $lastRoot = $root;
        $root = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    } while ($root !== $lastRoot);

    throw new Exception("Cannot find the root of the application, unable to run tests");
};
$root = $findRoot(__FILE__);
unset($findRoot);

chdir($root);
require_once $root . '/config/bootstrap.php';
EOF
        );
        file_put_contents(
            $this->supportDir . DIRECTORY_SEPARATOR . $actor . 'Helper.php',
            (new Helper($actor, $this->namespace))->produce()
        );
        file_put_contents("tests/$suite.suite.yml", $config);
    }
}

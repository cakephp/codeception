<?php
namespace Cake\Codeception\Command;

use Codeception\Lib\Generator\Helper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class Bootstrap extends \Codeception\Command\Bootstrap
{
    protected $namespace = 'App\Test';
    protected $actorSuffix = 'Tester';
    protected $helperDir = 'src/TestSuite/Codeception';
    protected $logDir = 'tmp/tests';
    protected $dataDir = 'tests/Fixture';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->namespace = rtrim($input->getOption('namespace'), '\\');

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

        if (file_exists('codeception.yml')) {
            $output->writeln("<error>\nProject is already initialized in '$path'\n</error>");
            return;
        }

        $output->writeln(
            "<fg=white;bg=magenta>Initializing Codeception in " . $realpath . "</fg=white;bg=magenta>\n"
        );

        if ($input->getOption('compat')) {
            $this->compatibilitySetup($output);
        } elseif ($input->getOption('customize')) {
            $this->customize($output);
        } else {
            $this->setup($output);
        }

        $output->writeln("<info>Building initial {$this->actorSuffix} classes</info>");
        $this->getApplication()->find('build')->run(
            new ArrayInput(array('command' => 'build')),
            $output
        );

        $output->writeln("<info>\nBootstrap is done. Check out " . $realpath . "/tests directory</info>");
    }

    public function createGlobalConfig()
    {
        $basicConfig = [
            'actor' => $this->actorSuffix,
            'paths'    => [
                'tests'   => 'tests',
                'log'     => $this->logDir,
                'data'    => $this->dataDir,
                'helpers' => $this->helperDir
            ],
            'settings' => [
                'bootstrap'    => 'bootstrap.php',
                'colors'       => (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN'),
                'memory_limit' => '1024M'
            ],
            'modules'  => [
                'enabled' => [
                    'Cake\Codeception\Helper',
                ],
            ]
        ];

        $str = Yaml::dump($basicConfig, 4);
        if ($this->namespace) {
            $str = "namespace: {$this->namespace} \n" . $str;
        }
        file_put_contents('codeception.yml', $str);
    }

    protected function createSuite($suite, $actor, $config)
    {
        @mkdir("tests/$suite");
        file_put_contents(
            "tests/$suite/bootstrap.php",
            "<?php\n// Here you can initialize variables that will be available to your tests\n"
        );
        file_put_contents(
            $this->helperDir . DIRECTORY_SEPARATOR . $actor . 'Helper.php',
            (new Helper($actor, $this->namespace))->produce()
        );
        file_put_contents("tests/$suite.suite.yml", $config);
    }

    protected function setup(OutputInterface $output)
    {
        $this->createGlobalConfig();
        $output->writeln("File codeception.yml created       <- global configuration");

        $this->createDirs();
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
}

<?php
namespace Cake\Codeception\Command;

use Cake\Codeception\Lib\Generator\Helper;
use Codeception\Command\Shared\FileSystem;
use Codeception\Command\Shared\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Create new test suite. Requires suite name and actor name
 *
 * * ``
 * * `codecept g:suite api` -> api + ApiTester
 * * `codecept g:suite integration Code` -> integration + CodeTester
 * * `codecept g:suite frontend Front` -> frontend + FrontTester
 *
 */
class GenerateSuite extends \Codeception\Command\GenerateSuite
{
    use FileSystem;
    use Config;

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $suite = ucfirst($input->getArgument('suite'));
        $actor = $input->getArgument('actor');

        if ($this->containsInvalidCharacters($suite)) {
            $output->writeln("<error>Suite name '$suite' contains invalid characters. ([A-Za-z0-9_]).</error>");
            return;
        }

        $config = \Codeception\Configuration::config($input->getOption('config'));
        if (!$actor) {
            $actor = $suite . $config['actor'];
        }
        $config['class_name'] = $actor;

        $dir = \Codeception\Configuration::testsDir();
        if (file_exists($dir . $suite . '.suite.yml')) {
            throw new \Exception("Suite configuration file '$suite.suite.yml' already exists.");
        }

        $this->buildPath($dir . $suite . DIRECTORY_SEPARATOR, 'bootstrap.php');

        // generate bootstrap
        $this->save(
            $dir . $suite . DIRECTORY_SEPARATOR . 'bootstrap.php',
            "<?php\n// Here you can initialize variables that will be available to your tests\n",
            true
        );
        $actorName = $this->removeSuffix($actor, $config['actor']);

        // generate helper
        $this->save(
            \Codeception\Configuration::helpersDir() . $actorName . 'Helper.php',
            (new Helper($actorName, $config['namespace']))->produce()
        );

        $enabledModules = [
            'Cake\Codeception\Helper',
            'App\TestSuite\Codeception\\' . $actorName . 'Helper',
        ];

        if ('Unit' === $suite) {
            array_shift($enabledModules);
        }

        $conf = [
            'class_name' => $actorName . $config['actor'],
            'modules' => [
                'enabled' => $enabledModules
            ]
        ];

        $this->save($dir . $suite . '.suite.yml', Yaml::dump($conf, 2));

        $output->writeln("<info>Suite $suite generated</info>");
    }

    private function containsInvalidCharacters($suite)
    {
        return preg_match('#[^A-Za-z0-9_]#', $suite) ? true : false;
    }
}

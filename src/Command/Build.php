<?php
namespace Cake\Codeception\Command;

use Cake\Codeception\Lib\Generator\Actor as ActorGenerator;

class Build extends \Codeception\Command\Build
{
    /**
     * Generates the actors (*Tester class) files.
     *
     * @param string $configFile Path to YAML configuration to use.
     */
    protected function buildActorsForConfig($configFile)
    {
        $config = $this->getGlobalConfig($configFile);
        $suites = $this->getSuites($configFile);

        $path = pathinfo($configFile);
        $dir = isset($path['dirname']) ? $path['dirname'] : getcwd();

        foreach ($config['include'] as $subConfig) {
            $this->output->writeln("<comment>Included Configuration: $subConfig</comment>");
            $this->buildActorsForConfig($dir . DIRECTORY_SEPARATOR . $subConfig);
        }

        if (!empty($suites)) {
            $this->output->writeln(sprintf(
                '<info>Building Actor classes for suites: %s</info>',
                implode(', ', $suites)
            ));
        }

        foreach ($suites as $suite) {
            $settings = $this->getSuiteConfig($suite, $configFile);
            $gen = new ActorGenerator($settings);
            $this->output->writeln(sprintf(
                '<info>%s</info> includes modules: %s',
                $gen->getActorName(),
                implode(', ', $gen->getModules())
            ));

            $contents = $gen->produce();

            @mkdir($settings['path'], 0755, true);
            $file = $settings['path'].$this->getClassName($settings['class_name']).'.php';
            $this->save($file, $contents, true);
            $this->output->writeln(sprintf(
                '%s.php generated succesfully, %s methods added',
                $settings['class_name'],
                $gen->getNumMethods()
            ));
        }
    }
}

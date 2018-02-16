<?php
namespace Cake\Codeception\Command;

use Cake\Codeception\Lib\Generator\Cest as CestGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCest extends \Codeception\Command\GenerateCest
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $suite = $input->getArgument('suite');
        $class = $input->getArgument('class');

        $config = $this->getSuiteConfig($suite);
        $className = $this->getShortClassName($class);
        $path = $this->createDirectoryFor($config['path'], $class);

        $filename = $this->completeSuffix($className, 'Cest');
        $filename = $path . $filename;

        if (file_exists($filename)) {
            $output->writeln("<error>Test $filename already exists</error>");
            return;
        }
        $gen = new CestGenerator($class, $config);
        $res = $this->createFile($filename, $gen->produce());
        if (!$res) {
            $output->writeln("<error>Test $filename already exists</error>");
            return;
        }

        $output->writeln("<info>Test was created in $filename</info>");
    }
}

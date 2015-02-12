<?php
namespace Cake\Codeception\Command;

use Cake\Codeception\Lib\Generator\Group as GroupGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateGroup extends \Codeception\Command\GenerateGroup
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getGlobalConfig($input->getOption('config'));
        $group = $input->getArgument('group');

        $class = ucfirst($group);
        $path = $this->buildPath($config['paths']['tests'] . '/Group/', $class);
        $filename = $this->completeSuffix($class, 'Group');
        $filename = $path . $filename;

        $this->introduceAutoloader(
            $config['paths']['tests'] . DIRECTORY_SEPARATOR . $config['settings']['bootstrap'],
            'Group',
            'Group'
        );

        $gen = new GroupGenerator($config, $group);
        $res = $this->save($filename, $gen->produce());

        if (!$res) {
            $output->writeln("<error>Group $filename already exists</error>");
            return;
        }

        $msg = [
            "<info>Group extension was created in $filename</info>",
            'To use this group extension, include it to "extensions" option of global Codeception config.'
        ];
        $output->writeln(implode("\n", $msg));
    }
}

<?php
namespace Cake\Codeception\Command;

use Cake\Codeception\Template\Bootstrap as BootstrapTemplate;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Bootstrap extends \Codeception\Command\Bootstrap
{

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $bootstrap = new BootstrapTemplate($input, $output);
        if ($input->getArgument('path')) {
            $bootstrap->initDir($input->getArgument('path'));
        }
        $bootstrap->setup();
    }
}

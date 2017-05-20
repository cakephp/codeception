<?php

namespace Cake\Codeception\Command;

use Cake\Codeception\Lib\Generator\Actor as ActorGenerator;

class Build extends \Codeception\Command\Build
{
    protected function buildPath($basePath, $testName)
    {
        // replace path, src/TestSuite/Codeception/ to tests/$suite/
        $name = preg_replace('/Tester$/', '', $testName);
        $searchPath = implode(DIRECTORY_SEPARATOR, ['src', 'TestSuite', 'Codeception', '']);
        $replacePath = implode(DIRECTORY_SEPARATOR, ['tests', $name, '']);
        $replacedBasePath = preg_replace('!' . preg_quote($searchPath, '!') . '!', $replacePath, $basePath);
        return parent::buildPath($replacedBasePath, $testName);
    }
}

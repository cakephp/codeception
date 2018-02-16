<?php
namespace Cake\Codeception\Console;

use Composer\Script\Event;

class Installer
{
    /**
     * Overwrites the `codecept` binary to use `Cake\Codeception` classes
     * where necessary.
     *
     * @param \Composer\Script\Event $event Event.
     */
    public static function customizeCodeceptionBinary(Event $event)
    {
        $binDir = dirname(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR;
        $binDir = $binDir . 'codeception' . DIRECTORY_SEPARATOR . 'codeception';
        $binFile = $binDir . DIRECTORY_SEPARATOR . 'codecept';
        $contents = file_get_contents($binFile);
        $from = 'new Codeception\\Command\\';
        $to = 'new Cake\\Codeception\\Command\\';

        $needles = [
            'Bootstrap',
            'GenerateCest',
            'GenerateSuite',
        ];

        foreach ($needles as $needle) {
            $contents = str_replace(
                $from . $needle,
                $to . $needle,
                $contents
            );
        }

        file_put_contents($binFile, $contents);
    }
}

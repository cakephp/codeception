<?php // @codingStandardsIgnoreFile

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

require dirname(__DIR__) . DS . 'vendor' . DS . 'autoload.php';

define('TEST_APP_ROOT', __DIR__ . DS . 'test_app' . DS);
define('TEST_APP_BIN', TEST_APP_ROOT . 'vendor' . DS . 'bin' . DS);

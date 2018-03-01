<?php
/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */
// fixes class conflict on PHPUnit >= 5.7
if (class_exists('\PHPUnit\Runner\Version') && version_compare(\PHPUnit\Runner\Version::id(), '5.7', '>=') &&
    !function_exists('loadPHPUnitAliases')) {
    function loadPHPUnitAliases() {
        // nothing to do
    }
}

if (getenv('db_dsn')) {
    putenv('DATABASE_URL=' . getenv('db_dsn'));
    putenv('DATABASE_TEST_URL=' . getenv('db_dsn'));
}

require dirname(__DIR__) . '/config/bootstrap.php';

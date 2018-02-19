<?php
/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */
// fixes class conflict on PHPUnit >= 6.0
if (class_exists('\PHPUnit\Runner\Version') && version_compare(\PHPUnit\Runner\Version::id(), '6.0', '>=') &&
    !function_exists('loadPHPUnitAliases')) {
    function loadPHPUnitAliases() {
        // nothing to do
    }
}

require dirname(__DIR__) . '/config/bootstrap.php';

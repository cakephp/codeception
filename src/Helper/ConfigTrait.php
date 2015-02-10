<?php
namespace Cake\Codeception\Helper;

use Cake\Core\Configure;

trait ConfigTrait
{

    /**
     * Asserts that a given key (and value) exist in the configuration.
     *
     * @param string|array $key Configuration key or array of key/values.
     * @param mixed $value Value to check for. If `NULL`, checks only key.
     */
    public function seeInConfig($key, $value = null)
    {
        if (is_array($key)) {
            array_walk($key, function ($v, $k) {
                $this->seeInConfig($k, $v);
            });
            return;
        }

        $message = "Could not find the $key config key";
        if (is_null($value)) {
            $this->assertTrue(Configure::check($key), $message);
            return;
        }

        $message .= ' with ' . json_encode($value);
        $this->assertEquals($value, Configure::read($key), $message);
    }

    /**
     * Asserts that a given key (and value) do not exist in the configuration.
     *
     * @param string|array $key Configuration key or array of key/values.
     * @param mixed $value Value to check for. If `NULL`, checks only key.
     */
    public function dontSeeInConfig($key, $value = null)
    {
        if (is_array($key)) {
            array_walk($key, function ($v, $k) {
                $this->dontSeeInConfig($k, $v);
            });
            return;
        }

        $message = "Unexpectedly managed to find the $key config key";
        if (is_null($value)) {
            $this->assertFalse(Configure::check($key), $message);
            return;
        }

        $message .= ' with ' . json_encode($value);
        $this->assertNotEquals($value, Configure::read($key), $message);
    }
}

<?php
namespace Cake\Codeception\Helper;

trait SessionTrait
{

    /**
     * Inserts key/value(s) in the session.
     *
     * @param string|array $key Session key or array of key/values.
     * @param mixed $value Value.
     */
    public function haveInSession($key, $value = null)
    {
        $this->grabService('session')->write($key, $value);
    }

    /**
     * Asserts that given key (and value) exist in the session.
     *
     * @param string|array $key Session key or array of key/values.
     * @param mixed $value Value to check for. If `NULL`, check only key.
     */
    public function seeInSession($key, $value = null)
    {
        if (is_array($key)) {
            array_walk($key, function ($v, $k) {
                if (is_int($k)) {
                    $k = $v;
                    $v = null;
                }
                $this->seeInSession($k, $v);
            });
            return;
        }

        $session = $this->grabService('session');

        if (is_null($value)) {
            $this->assertTrue($session->check($key));
            return;
        }

        $this->assertEquals($value, $session->read($key));
    }

    /**
     * Asserts that given key (and value) do not exist in the session.
     *
     * @param string|array $key Session key or array of key/values.
     * @param mixed $value Value to check for. If `NULL`, check only key.
     */
    public function dontSeeInSession($key, $value = null)
    {
        if (is_array($key)) {
            array_walk($key, function ($v, $k) {
                if (is_int($k)) {
                    $k = $v;
                    $v = null;
                }
                $this->dontSeeInSession($k, $v);
            });
            return;
        }

        $session = $this->grabService('session');

        if (is_null($value)) {
            $this->assertFalse($session->check($key));
            return;
        }

        $this->assertNotEquals($value, $session->read($key));
    }
}

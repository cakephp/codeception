<?php
namespace Cake\Codeception\Helper;

trait SessionTrait
{

    public function seeSessionHasValues(array $bindings)
    {
        foreach ($bindings as $key => $value) {
            if (is_int($key)) {
                $this->seeInSession($value);
                continue;
            }
            $this->seeInSession($key, $value);
        }
    }

    public function seeInSession($key, $value = null)
    {
        if (is_array($key)) {
            $this->sessionHasValues($key);
            return;
        }

        if (is_null($value)) {
            $this->assertTrue($this->client->cake['request']->session()->check($key));
            return;
        }

        $this->assertEquals($value, $this->client->cake['request']->session()->read($key));
    }

    public function dontSeeInSession($key, $value = null)
    {
        if (is_null($value)) {
            $this->assertFalse($this->client->cake['request']->session()->check($key));
            return;
        }

        $this->assertNotEquals($value, $this->client->cake['request']->session()->read($key));
    }

    public function seeSessionErrorMessage(array $bindings)
    {
        // Not yet implemented.
    }

    public function seeSessionHasErrors()
    {
        // Not yet implemented.
    }
}

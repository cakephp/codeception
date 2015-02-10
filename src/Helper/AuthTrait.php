<?php
namespace Cake\Codeception\Helper;

trait AuthTrait
{

    public function amLoggedAs($user, $driver = null)
    {
        $this->client->getSession()->write(['Auth' => ['User' => $user]]);
    }

    public function logout()
    {
        if (empty($this->client->cake['auth'])) {
            $this->fail('The AuthComponent is not loaded');
        }
        $this->client->cake['auth']->logout();
    }

    public function seeAuthentication()
    {
        if (empty($this->client->cake['auth'])) {
            $this->fail('The AuthComponent is not loaded');
        }
        $this->assertTrue(is_array($this->client->cake['auth']->user()), 'User is not logged in');
    }

    public function dontSeeAuthentication()
    {
        if (empty($this->client->cake['auth'])) {
            $this->fail('The AuthComponent is not loaded');
        }
        $this->assertFalse($this->client->getSession()->check('Auth.User'), 'User is logged in');
    }
}

<?php
namespace Cake\Codeception\Helper;

trait AuthTrait
{

    /**
     * Sets the currently logged in user for the application. Takes a user
     * data array to store in the session.
     *
     * This method works but is NOT complete.
     *
     * @param array $user User data.
     */
    public function amLoggedAs($user)
    {
        $this->client->getSession()->write(['Auth' => ['User' => $user]]);
    }

    /**
     * Logs user out.
     *
     * @return void
     */
    public function logout()
    {
        if (empty($this->client->cake['auth'])) {
            $this->fail('The AuthComponent is not loaded');
        }
        $this->client->cake['auth']->logout();
    }

    /**
     * Asserts request has logged in user.
     */
    public function seeAuthentication()
    {
        if (empty($this->client->cake['auth'])) {
            $this->fail('The AuthComponent is not loaded');
        }
        $this->assertTrue(is_array($this->client->cake['auth']->user()), 'User is not logged in');
    }

    /**
     * Asserts request has no logged in user.
     */
    public function dontSeeAuthentication()
    {
        if (empty($this->client->cake['auth'])) {
            $this->fail('The AuthComponent is not loaded');
        }
        $this->assertFalse($this->client->getSession()->check('Auth.User'), 'User is logged in');
    }
}

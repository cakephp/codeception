<?php
namespace Cake\Codeception;

use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\TestSuite\Fixture\FixtureManager;
use Codeception\Lib\Framework;
use Codeception\TestCase;

class Helper extends Framework
{
    protected $_currentConfiguration;

    protected $fixtureManager = null;
    protected $testCase = null;

    public $config = [
        'autoFixtures' => true,
        'dropTables' => false,
    ];

    public function _before(TestCase $test)
    {
        $this->testCase = $test->getTestClass();
        if (!isset($this->testCase->autoFixtures)) {
            $this->testCase->autoFixtures = $this->config['autoFixtures'];
        }
        if (!isset($this->testCase->dropTables)) {
            $this->testCase->dropTables = $this->config['dropTables'];
        }

        EventManager::instance(new EventManager());
        $this->fixtureManager = new FixtureManager();

        if ($this->testCase->autoFixtures) {
            if (!isset($this->testCase->fixtures)) {
                $this->testCase->fixtures = [];
            }
            $this->loadFixtures($this->testCase->fixtures);
        }

        $this->client = $this->_getConnectorInstance();

        $this->_snapshotApplication();
        $this->_reloadRoutes();
    }

    public function _after(TestCase $test)
    {
        $this->_resetApplication();
        $this->fixtureManager->unload($this->testCase);
        if ($test->getTestClass()->dropTables) {
            $this->fixtureManager->shutDown();
        }
    }

    public function loadFixtures($fixtures)
    {
        if (func_num_args() > 1) {
            $fixtures = func_get_args();
        }
        $this->testCase->fixtures = $fixtures;
        $this->fixtureManager->fixturize($this->testCase);
        $this->fixtureManager->load($this->testCase);
    }

    public function haveEnabledFilters()
    {
        // Not yet implemented.
    }

    public function haveDisabledFilters()
    {
        // Not yet implemented.
    }

    public function expectedCakePHPVersion($ver)
    {
        // Not yet implemented.
    }

    public function amOnRoute($route, $params = [])
    {
        $params += ['_method' => 'GET'];
        $this->amOnPage(Router::url($route + $params));
    }

    public function amOnAction($action, $params = [])
    {
        list($c, $a) = explode('@', $action);
        $this->amOnRoute([
            'controller' => str_replace('Controller', '', $c),
            'action' => $a,
        ] + $params);
    }

    public function seeCurrentRouteIs($route, $params = [])
    {
        $this->seeCurrentUrlEquals(Router::url($route + $params));
    }

    public function seeCurrentActionIs($action, $params = [])
    {
        list($c, $a) = explode('@', $action);
        $this->seeCurrentUrlEquals([
            'controller' => str_replace('Controller', '', $c),
            'action' => $a,
        ] + $params);
    }

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

    public function grabService($class)
    {
        return $this->client->cake[$class];
    }

    public function seeLayout($layout)
    {
        // Not implemented yet.
    }

    public function seeView($view)
    {
        // Not implemented yet.
    }

    public function seeViewVar($name, $value = null)
    {
        // Not implemented yet.
    }

    public function haveRecord($model, $attributes = [])
    {
        return TableRegistry::get($model)
            ->newEntity($attributes)
            ->isNew();
    }

    public function seeRecord($model, $attributes = [])
    {
        $record = $this->findRecord($model, $attributes);
        if (!$record) {
            $this->fail("Couldn't find $model with " . json_encode($attributes));
        }
        $this->debugSection($model, json_encode($record));
    }

    public function dontSeeRecord($model, $attributes = [])
    {
        $record = $this->findRecord($model, $attributes);
        $this->debugSection($model, json_encode($attributes));
        if ($record) {
            $this->fail("Unexpectedly managed to find $model with " . json_encode($attributes));
        }
    }

    public function grabRecord($model, $attributes = [])
    {
        $this->findRecord($model, $attributes);
    }

    public function findRecord($model, $attributes = [])
    {
        return TableRegistry::get($model)
            ->find()
            ->where($attributes)
            ->first();
    }

    protected function _reloadRoutes()
    {
        Router::reload();
    }

    protected function _resetApplication()
    {
        if (!empty($this->_currentConfiguration)) {
            Configure::clear();
            Configure::write($this->_currentConfiguration);
        }
    }

    protected function _snapshotApplication()
    {
        if (empty($this->_currentConfiguration)) {
            $this->_currentConfiguration = Configure::read();
        }
    }

    protected function _getConnectorInstance(array $server = [])
    {
        return new Connector($server);
    }
}

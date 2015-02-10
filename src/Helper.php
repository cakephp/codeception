<?php
namespace Cake\Codeception;

use Cake\Codeception\Helper\AuthTrait;
use Cake\Codeception\Helper\DbTrait;
use Cake\Codeception\Helper\DispatcherTrait;
use Cake\Codeception\Helper\RouterTrait;
use Cake\Codeception\Helper\SessionTrait;
use Cake\Codeception\Helper\ViewTrait;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\TestSuite\Fixture\FixtureManager;
use Codeception\Lib\Framework;
use Codeception\TestCase;

class Helper extends Framework
{

    use AuthTrait;
    use DbTrait;
    use DispatcherTrait;
    use RouterTrait;
    use SessionTrait;
    use ViewTrait;

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

    public function expectedCakePHPVersion($ver)
    {
        // Not yet implemented.
    }

    public function grabService($class)
    {
        return $this->client->cake[$class];
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

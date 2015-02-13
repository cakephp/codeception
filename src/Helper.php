<?php
namespace Cake\Codeception;

use Cake\Codeception\Helper\AuthTrait;
use Cake\Codeception\Helper\ConfigTrait;
use Cake\Codeception\Helper\DbTrait;
use Cake\Codeception\Helper\DispatcherTrait;
use Cake\Codeception\Helper\RouterTrait;
use Cake\Codeception\Helper\SessionTrait;
use Cake\Codeception\Helper\ViewTrait;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\Routing\Router;
use Cake\TestSuite\Fixture\FixtureManager;
use Codeception\Lib\Framework;
use Codeception\TestCase;

class Helper extends Framework
{

    use AuthTrait;
    use ConfigTrait;
    use DbTrait;
    use DispatcherTrait;
    use RouterTrait;
    use SessionTrait;
    use ViewTrait;

    /**
     * Module's default configuration.
     *
     * @var array
     */
    public $config = [
        'autoFixtures' => true,
        'dropTables' => false,
    ];

    /**
     * The class responsible for managing the creation, loading and removing of fixtures
     *
     * @var \Cake\TestSuite\Fixture\FixtureManager
     */
    protected $fixtureManager = null;

    /**
     * Configure values to restore at end of test.
     *
     * @var array
     */
    protected $configure = [];

    protected $testCase = null;

// @codingStandardsIgnoreStart
    public function _before(TestCase $test)
    {
        if (method_exists($test, 'getTestClass')) {
            $this->testCase = $test->getTestClass();
        } else {
            $this->testCase = $test;
        }

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

        $this->client = $this->getConnectorInstance();

        $this->snapshotApplication();
        $this->reloadRoutes();
    }

    public function _after(TestCase $test)
    {
        $this->resetApplication();
        $this->fixtureManager->unload($this->testCase);
        if ($this->testCase->dropTables) {
            $this->fixtureManager->shutDown();
        }
    }
// @codingStandardsIgnoreEnd

    public function loadFixtures($fixtures)
    {
        if (func_num_args() > 1) {
            $fixtures = func_get_args();
        }
        $this->testCase->fixtures = $fixtures;
        $this->fixtureManager->fixturize($this->testCase);
        $this->fixtureManager->load($this->testCase);
    }

    /**
     * Asserts the expected CakePHP version.
     *
     * @param string $ver Expected version.
     * @param string $operator Comparison to run, defaults to greater or equal.
     * @
     */
    public function expectedCakePHPVersion($ver, $operator = 'ge')
    {
        $this->assertTrue(version_compare($ver, Configure::version(), $operator));
    }

    /**
     * Returns one of the instantiated services
     *
     * @param [type] $class [description]
     * @return object Cake object of requested type.
     * @see \Cake\Codeception\Connector::$cake
     */
    public function grabService($class)
    {
        return $this->client->cake[$class];
    }

    /**
     * Reloads the defined routes.
     *
     * @return void
     */
    protected function reloadRoutes()
    {
        Router::reload();
    }

    /**
     * Resets the application's configuration.
     *
     * @return void
     */
    protected function resetApplication()
    {
        if (!empty($this->configure)) {
            Configure::clear();
            Configure::write($this->configure);
        }
    }

    /**
     * Snapshots the application's configuration.
     *
     * @return void
     */
    protected function snapshotApplication()
    {
        if (empty($this->configure)) {
            $this->configure = Configure::read();
        }
    }

    /**
     * Instantiate a connector.
     *
     * @param array $server
     * @return \Cake\Codeception\Connector
     */
    protected function getConnectorInstance(array $server = [])
    {
        return new Connector($server);
    }
}

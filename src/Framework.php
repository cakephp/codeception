<?php
namespace Cake\Codeception;

use Cake\Codeception\Helper\ConfigTrait;
use Cake\Codeception\Helper\DbTrait;
use Cake\Codeception\Helper\ORMTrait;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\TestSuite\Fixture\FixtureManager;
use Codeception\TestCase;

class Framework extends \Codeception\Lib\Framework
{

    use ConfigTrait;
    use DbTrait;
    use ORMTrait;

    /**
     * Module's default configuration.
     *
     * @var array
     */
    public $config = [
        'autoFixtures' => true,
        'dropTables' => false,
        'cleanUpInsertedRecords' => true,
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
    public function _before(TestCase $test) // @codingStandardsIgnoreEnd
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

        if (!isset($this->testCase->cleanUpInsertedRecords)) {
            $this->testCase->cleanUpInsertedRecords = $this->config['cleanUpInsertedRecords'];
        }

        EventManager::instance(new EventManager());
        $this->fixtureManager = new FixtureManager();

        if ($this->testCase->autoFixtures) {
            if (!isset($this->testCase->fixtures)) {
                $this->testCase->fixtures = [];
            }
            $this->loadFixtures($this->testCase->fixtures);
        }

        $this->snapshotApplication();
    }

    // @codingStandardsIgnoreStart
    public function _after(TestCase $test) // @codingStandardsIgnoreEnd
    {
        $this->resetApplication();

        if ($this->testCase->cleanUpInsertedRecords) {
            $this->cleanUpInsertedRecords();
        }

        $this->fixtureManager->unload($this->testCase);
        if ($this->testCase->dropTables) {
            $this->fixtureManager->shutDown();
        }
    }

    /**
     * Chooses which fixtures to load for a given test
     *
     * @return void
     */
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
}

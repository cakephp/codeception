<?php

namespace Cake\Codeception\Helper;

use Cake\TestSuite\Fixture\FixtureManager;
use Codeception\TestInterface;
use Codeception\Test\Cest;
use Exception;

/**
 * CakePHP Fixture loader trait
 *
 * @see Cake\TestSuite\Fixture\FixtureInjector
 */
trait FixtureTrait
{

    /**
     * The instance of the fixture manager to use
     *
     * @var FixtureManager
     */
    protected $fixtureManager;

    /**
     * Current TestCase
     *
     * @var stdClass
     */
    protected $testCase;

    /**
     * Load FixtureManager. call at _initialize()
     *
     * @return void
     */
    protected function loadFixtureManager()
    {
        $manager = new FixtureManager();
        $manager->setDebug($this->_getConfig('debug'));

        $this->fixtureManager = $manager;
        $this->fixtureManager->shutDown();

        $this->debugSection('Fixture', 'Initialized FixtureManager, debug=' . (int)$this->_getConfig('debug'));
    }

    /**
     * Destroys the fixtures created by the fixture manager at the end of
     * the test suite run. call at _afterSuite()
     *
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function shutDownFixtureManager()// @codingStandardsIgnoreEnd
    {
        $this->testCase = null;
        $this->fixtureManager->shutDown();
        $this->debugSection('Fixture', 'FixtureManager shutDown');
    }

    /**
     * Adds fixtures to a test case when it starts. call in _before()
     *
     * @param TestInterface $test The test case
     * @return void
     */
    protected function loadFixture(TestInterface $test)
    {
        if ($this->hasFixtures($test)) {
            $this->debugSection('Fixture', 'Test class is: ' . get_class($test->getTestClass()));
            $this->shutDownIfDbModuleLoaded();
            $this->testCase = $this->setRequireProperties($test->getTestClass());
            $this->fixtureManager->fixturize($this->testCase);

            $this->debugSection('Fixture', 'Load fixtures: ' . implode(', ', $this->testCase->fixtures));
            $this->fixtureManager->load($this->testCase);
        }
    }

    /**
     * Unloads fixtures from the test case. call in _after()
     *
     * @param TestInterface $test The test case
     * @return void
     */
    protected function unloadFixture(TestInterface $test)
    {
        if ($this->hasFixtures($test)) {
            $this->debugSection('Fixture', 'Unload fixtures: ' . implode(', ', $test->getTestClass()->fixtures));
            $this->fixtureManager->unload($test->getTestClass());
        }

        $this->testCase = null;
    }

    /**
     * Chooses which fixtures to load for a given test
     *
     * Each parameter is a model name that corresponds to a fixture, i.e. 'Posts', 'Authors', etc.
     * Passing no parameters will cause all fixtures on the test case to load.
     *
     * @return void
     * @see Cake\TestSuite\TestCase::loadFixtures()
     * @throws Exception when no fixture manager is available.
     */
    public function loadFixtures()
    {
        $args = func_get_args();
        foreach ($args as $class) {
            $this->fixtureManager->loadSingle($class, null, $this->testCase->dropTables);
        }

        if (empty($args)) {
            $autoFixtures = $this->testCase->autoFixtures;
            $this->testCase->autoFixtures = true;
            $this->fixtureManager->load($this);
            $this->testCase->autoFixtures = $autoFixtures;
        }
    }

    /**
     * check the test class has $fixtures
     *
     * @param Cest $test a Cest object
     * @return bool
     */
    private function hasFixtures($test)
    {
        return $test instanceof Cest && property_exists($test->getTestClass(), 'fixtures');
    }

    /**
     * set required properties to a given test class
     *
     * @param stdClass $testClass Target test case
     * @return stdClass
     */
    private function setRequireProperties($testClass)
    {
        if (!property_exists($testClass, 'autoFixtures')) {
            $testClass->autoFixtures = $this->_getConfig('autoFixtures');
        }
        if (!property_exists($testClass, 'dropTables')) {
            $testClass->dropTables = $this->_getConfig('dropTables');
        }

        return $testClass;
    }

    /**
     * Shutdown FixtureManager if Db module loaded
     *
     * @return void
     */
    private function shutDownIfDbModuleLoaded()
    {
        if (!$this->hasModule('Db')) {
            return;
        }

        $db = $this->getModule('Db');
        /* @var $db Db */

        if ($db->_getConfig('cleanup') && $db->isPopulated()) {
            // Shutdown FixtureManager, If reseted database by Db modle
            $this->fixtureManager->shutDown();
        }
    }
}

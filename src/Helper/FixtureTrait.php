<?php

namespace Cake\Codeception\Helper;

use Cake\TestSuite\Fixture\FixtureManager;
use Codeception\Exception\ModuleException;
use Codeception\TestInterface;
use Codeception\Test\Cest;
use Exception;
use PHPUnit_Framework_MockObject_Generator;

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
     * Current fixture holder
     *
     * @var stdClass
     */
    protected $fixtureHolder;

    /**
     * Current test filename
     *
     * @var string
     */
    protected $testFilename;

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
        $this->fixtureHolder = null;
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
        $this->testFilename = $test->getMetadata()->getFilename();

        if ($this->isCestHasFixtures($test)) {
            $this->debugSection('Fixture', 'Test class is: ' . get_class($test->getTestClass()));
            $this->shutDownIfDbModuleLoaded();
            $this->fixtureHolder = $this->setupFixtureHolder($test->getTestClass());
            $this->fixtureManager->fixturize($this->fixtureHolder);

            $this->debugSection('Fixture', 'Load fixtures: ' . implode(', ', $this->fixtureHolder->fixtures));
            $this->fixtureManager->load($this->fixtureHolder);
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
        if ($this->fixtureHolder) {
            $this->debugSection('Fixture', 'Unload fixtures: ' . implode(', ', $this->fixtureHolder->fixtures));
            $this->fixtureManager->unload($this->fixtureHolder);
        }

        $this->fixtureHolder = null;
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
        if (!$this->fixtureHolder) {
            $message = 'Can\'t load fixtures. the fixtures not initialized,';
            $message .= ' You should call $I->useFixtures() before using this method.';
            throw new ModuleException(__CLASS__, $message);
        }

        $args = $this->flattenFixureArgs(func_get_args());

        foreach ($args as $class) {
            $this->fixtureManager->loadSingle($class, null, $this->fixtureHolder->dropTables);
        }

        if (empty($args)) {
            $autoFixtures = $this->fixtureHolder->autoFixtures;
            $this->fixtureHolder->autoFixtures = true;
            $this->fixtureManager->load($this->fixtureHolder);
            $this->fixtureHolder->autoFixtures = $autoFixtures;
        }
    }

    /**
     * Setup fixtures to load for a given test case
     *
     * Each parameter is a fixture specific name, like CakePHP's
     * TestCase::$fixtures, i.e. 'app.posts', 'app.authors', etc.
     *
     * @return void
     * @throws Exception when no fixture manager is available.
     */
    public function useFixtures()
    {
        if ($this->fixtureHolder) {
            throw new ModuleException(__CLASS__, 'Already fixtures initialized, in the test.');
        }

        $args = $this->flattenFixureArgs(func_get_args());

        $holder = $this->getFixtureHolder();
        $testCase = $this->setupFixtureHolder($holder);
        $testCase->fixtures = $args;
        $this->fixtureHolder = $testCase;

        $this->fixtureManager->fixturize($this->fixtureHolder);
        $this->debugSection('Fixture', 'Use fixtures: ' . implode(', ', $this->fixtureHolder->fixtures));
    }

    /**
     * flatten args
     *
     * @param array $args received function args
     * @return array
     */
    private function flattenFixureArgs(array $args)
    {
        $normalized = [];

        foreach ($args as $arg) {
            if (is_array($arg)) {
                $normalized += $arg;
            } else {
                $normalized[] = $arg;
            }
        }

        return array_unique($normalized);
    }

    /**
     * generate temporary fixture holder
     *
     * @return \stdClass
     */
    private function getFixtureHolder()
    {
        $className = 'CakeFixtureMock_' . hash('md5', $this->testFilename);

        return (new PHPUnit_Framework_MockObject_Generator)->getMock('\stdClass', [], [], $className);
    }

    /**
     * check the test class has $fixtures
     *
     * @param TestInterface $test a Test object
     * @return bool
     */
    private function isCestHasFixtures($test)
    {
        return $test instanceof Cest && property_exists($test->getTestClass(), 'fixtures');
    }

    /**
     * set required properties to a given test class
     *
     * @param stdClass $testClass Target test case
     * @return stdClass
     */
    private function setupFixtureHolder($testClass)
    {
        if (!property_exists($testClass, 'autoFixtures')) {
            $testClass->autoFixtures = $this->_getConfig('autoFixtures');
        }
        if (!property_exists($testClass, 'dropTables')) {
            $testClass->dropTables = $this->_getConfig('dropTables');
        }
        if (!property_exists($testClass, 'fixtures')) {
            $testClass->fixtures = [];
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

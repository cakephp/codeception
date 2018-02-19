<?php
namespace Cake\Codeception;

use Cake\Codeception\Helper\ConfigTrait;
use Cake\Codeception\Helper\DbTrait;
use Cake\Codeception\Helper\FixtureTrait;
use Cake\Codeception\Helper\ORMTrait;
use Cake\Core\Configure;
use Codeception\TestInterface;

class Framework extends \Codeception\Lib\Framework
{

    use ConfigTrait;
    use DbTrait;
    use ORMTrait;
    use FixtureTrait;

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
     * Configure values to restore at end of test.
     *
     * @var array
     */
    protected $configure = [];

    // @codingStandardsIgnoreStart
    public function _initialize()// @codingStandardsIgnoreEnd
    {
        $this->loadFixtureManager();
    }

    // @codingStandardsIgnoreStart
    public function _afterSuite()// @codingStandardsIgnoreEnd
    {
        $this->shutDownFixtureManager();
    }

    // @codingStandardsIgnoreStart
    public function _before(TestInterface $test) // @codingStandardsIgnoreEnd
    {
        $this->loadFixture($test);
        $this->snapshotApplication();
    }

    // @codingStandardsIgnoreStart
    public function _after(TestInterface $test) // @codingStandardsIgnoreEnd
    {
        $this->resetApplication();

        if ($this->config['cleanUpInsertedRecords']) {
            $this->cleanUpInsertedRecords();
        }

        $this->unloadFixture($test);
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

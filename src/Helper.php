<?php
namespace Cake\Codeception;

use Cake\Codeception\Helper\AuthTrait;
use Cake\Codeception\Helper\DispatcherTrait;
use Cake\Codeception\Helper\RouterTrait;
use Cake\Codeception\Helper\SessionTrait;
use Cake\Codeception\Helper\ViewTrait;
use Cake\Routing\Router;
use Codeception\TestCase;

class Helper extends Framework
{

    use AuthTrait;
    use DispatcherTrait;
    use RouterTrait;
    use SessionTrait;
    use ViewTrait;

    // @codingStandardsIgnoreStart
    public function _before(TestCase $test) // @codingStandardsIgnoreEnd
    {
        parent::_before($test);
        $this->client = $this->getConnectorInstance();
        $this->reloadRoutes();
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

<?php
namespace Cake\Codeception;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Network\Session;
use Cake\Routing\DispatcherFactory;
use Cake\Routing\Router;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\Request as BrowserKitRequest;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;

class Connector extends Client
{
    /**
     * Associative array of CakePHP classes:
     *
     *  - request: \Cake\Network\Request
     *  - response: \Cake\Network\Response
     *  - session: \Cake\Network\Session
     *  - controller: \Cake\Controller\Controller
     *  - view: \Cake\View\View
     *  - auth: \Cake\Controller\Component\AuthComponent
     *  - cookie: \Cake\Controller\Component\CookieComponent
     *
     * @var array
     */
    public $cake;

    /**
     * Get instance of the session.
     *
     * @return \Cake\Network\Session
     */
    public function getSession()
    {
        if (!empty($this->cake['session'])) {
            return $this->cake['session'];
        }

        if (!empty($this->cake['request'])) {
            $this->cake['session'] = $this->cake['request']->session();
            return $this->cake['session'];
        }

        $config = (array)Configure::read('Session') + ['defaults' => 'php'];
        $this->cake['session'] = Session::create($config);
        return $this->cake['session'];
    }

    /**
     * Filters the BrowserKit request to the cake one.
     *
     * @param \Symfony\Component\BrowserKit\Request $request BrowserKit request.
     * @return \Cake\Network\Request Cake request.
     */
    protected function filterRequest(BrowserKitRequest $request)
    {
        $url = preg_replace('/^https?:\/\/[a-z0-9\-\.]+/', '', $request->getUri());

        $_ENV = $environment = ['REQUEST_METHOD' => $request->getMethod()] + $request->getServer();

        $props = [
            'url' => $url,
            'post' => (array)$request->getParameters(),
            'files' => (array)$request->getFiles(),
            'cookies' => (array)$request->getCookies(),
            'session' => $this->getSession(),
            'environment' => $environment,
        ];

        if (class_exists('\Cake\Http\ServerRequest') && $this->hasApplicationClass()) {
            // CakePHP >= 3.4
            $this->cake['request'] = new \Cake\Http\ServerRequest($props);
        } else {
            // CakePHP <= 3.3 or Non PSR-7 dispatcher
            $this->cake['request'] = new Request($props);

            // set params
            Router::setRequestInfo($this->cake['request']);
            $this->cake['request']->params = Router::parse($url);
        }

        return $this->cake['request'];
    }

    /**
     * Filters the cake response to the BrowserKit one.
     *
     * @param \Cake\Network\Response $response Cake response.
     * @return \Symfony\Component\BrowserKit\Response BrowserKit response.
     */
    protected function filterResponse($response)
    {
        $this->cake['response'] = $response;
        if (is_a($response, '\Zend\Diactoros\Response') && class_exists('\Cake\Http\ResponseTransformer')) {
            $response = \Cake\Http\ResponseTransformer::toCake($response);
        }

        foreach ($response->cookie() as $cookie) {
            $this->getCookieJar()->set(new Cookie(
                $cookie['name'],
                $cookie['value'],
                $cookie['expire'],
                $cookie['path'],
                $cookie['domain'],
                $cookie['secure'],
                $cookie['httpOnly']
            ));
        }

        $response->sendHeaders();
        return new BrowserKitResponse(
            $response->body(),
            $response->statusCode(),
            $response->header()
        );
    }

    /**
     * Makes a request.
     *
     * @param \Cake\Network\Request $request Cake request.
     * @return \Cake\Network\Response Cake response.
     */
    protected function doRequest($request)
    {
        $response = new Response();

        try {
            if (is_a($request, '\Cake\Http\ServerRequest')) {
                // Run with PSR-7 dispatcher
                $response = $this->runApplication($request);
            } else {
                // Run with legacy dispatcher
                $response = $this->runDispatcher($request, $response);
            }
        } catch (\PHPUnit_Exception $e) {
            throw $e;
        } catch (\Exception $e) {
            $response = $this->handleError($e);
        }

        return $response;
    }

    /**
     * Run application CakePHP < 3.4
     *
     * @param \Cake\Network\Request $request Cake request.
     * @return \Cake\Network\Response Cake response.
     */
    protected function runDispatcher($request, $response)
    {
        $dispatcher = DispatcherFactory::create();
        $dispatcher->eventManager()->on(
            'Dispatcher.beforeDispatch',
            ['priority' => 999],
            [$this, 'controllerSpy']
        );
        ob_start();
        $dispatcher->dispatch($request, $response);
        ob_end_clean();

        return $response;
    }

    /**
     * Run application CakePHP >= 3.4
     *
     * @return \Cake\Http\Response Cake response.
     */
    protected function runApplication($request)
    {
        $applicationClass = $this->getApplicationClassName();
        $server = new \Cake\Http\Server(new $applicationClass(CONFIG));

        $server->eventManager()->on(
            'Dispatcher.beforeDispatch',
            ['priority' => 999],
            [$this, 'controllerSpy']
        );
        $response = $server->run($request);

        return $response;
    }

    /**
     * Attempts to render an error response for a given exception.
     *
     * This method will attempt to use the configured exception renderer.
     * If that class does not exist, the built-in renderer will be used.
     *
     * @param \Exception $exception Exception to handle.
     * @return void
     * @throws \Exception
     */
    protected function handleError($exception)
    {
        $class = Configure::read('Error.exceptionRenderer');
        if (empty($class) || !class_exists($class)) {
            $class = 'Cake\Error\ExceptionRenderer';
        }
        $instance = new $class($exception);
        return $instance->render();
    }

    /**
     * [controllerSpy description]
     * @param \Cake\Event\Event $event Event.
     */
    public function controllerSpy(Event $event)
    {
        if (empty($event->data['controller'])) {
            return;
        }

        $this->cake['controller'] = $event->data['controller'];
        $eventManager = $event->data['controller']->eventManager();

        $eventManager->on(
            'Controller.startup',
            ['priority' => 999],
            [$this, 'authSpy']
        );


        $eventManager->on(
            'View.beforeRender',
            ['priority' => 999],
            [$this, 'viewSpy']
        );
    }

    /**
     * [authSpy description]
     * @param \Cake\Event\Event $event Event.
     */
    public function authSpy(Event $event)
    {
        if ($event->subject()->Auth) {
            $this->cake['auth'] = $event->subject()->Auth;
        }
    }

    /**
     * [viewSpy description]
     * @param \Cake\Event\Event $event Event.
     */
    public function viewSpy(Event $event)
    {
        $this->cake['view'] = $event->subject();
    }

    /**
     * Get Application class name
     *
     * @return string
     */
    protected function getApplicationClassName()
    {
        return '\\' . Configure::read('App.namespace') . '\Application';
    }

    /**
     * App has Application class
     *
     * @return bool
     */
    protected function hasApplicationClass()
    {
        return class_exists('\\' . Configure::read('App.namespace') . '\Application');
    }
}

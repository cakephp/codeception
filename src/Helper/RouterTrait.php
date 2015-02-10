<?php
namespace Cake\Codeception\Helper;

use Cake\Routing\Router;
use InvalidArgumentException;

trait RouterTrait
{

    /**
     * Opens web page using route array (or `_name`) and parameters.
     *
     * @param array|string $route Route's array or name.
     * @param array $params Extra route parameters (i.e. prefix, _method, etc.)
     */
    public function amOnRoute($route, $params = [])
    {
        if (!is_array($route)) {
            $route = ['_name' => $route];
        }

        $this->amOnPage(Router::url($route + $params + ['_method' => 'GET']));
    }

    /**
     * Opens web page using action name and parameters.
     *
     * @param string $action String notation of the route (i.e. PostsController.add, Posts.add, posts.add).
     * @param array $params Extra route parameters (i.e. prefix, _method, etc.).
     */
    public function amOnAction($action, $params = [])
    {
        $this->amOnRoute($this->routeFromAction($action), $params);
    }

    /**
     * Asserts that current url matches route.
     *
     * @param array|string $route Route's array or name.
     * @param array $params Extra route parameters (i.e. prefix, _method, etc.)
     */
    public function seeCurrentRouteIs($route, $params = [])
    {
        if (!is_array($route)) {
            $route = ['_name' => $route];
        }

        $this->seeCurrentUrlEquals(Router::url($route + $params));
    }

    /**
     * Asserts that current url matches action.
     *
     * @param string $action String notation of the route (i.e. PostsController.add, Posts.add, posts.add).
     * @param array $params Extra route parameters (i.e. prefix, _method, etc.)
     */
    public function seeCurrentActionIs($action, $params = [])
    {
        $this->seeCurrentRouteIs($this->routeFromAction($action), $params);
    }

    /**
     * Returns a route array from an action string notation.
     *
     * @param string $action String notation of the route (i.e. PostsController.add, Posts.add, posts.add).
     * @return array
     */
    protected function routeFromAction($action)
    {
        $parts = [];

        foreach (['@', '.'] as $delimiter) {
            if (mb_strpos($action, $delimiter)) {
                $parts = explode($delimiter, $action);
                break;
            }
        }

        if (count($parts) < 2) {
            throw new InvalidArgumentException(sprintf('Invalid action name [%s]', $action));
        }

        list($controller, $action) = $parts;
        $controller = str_replace('Controller', '', $controller);
        return compact('controller', 'action');
    }
}

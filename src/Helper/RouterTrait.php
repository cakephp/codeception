<?php
namespace Cake\Codeception\Helper;

trait RouterTrait
{

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
}

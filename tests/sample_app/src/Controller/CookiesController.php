<?php
namespace App\Controller;

class CookiesController extends AppController
{
    public $components = ['Cookie'];

    public function index()
    {
        $this->Cookie->write('foo', 'bar');
    }
}

<?php

namespace Rifle\Controllers;

use Rifle\View;
use Symfony\Component\HttpFoundation\Request;

class BaseController
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var View
     */
    protected $view;

    /**
     * @var string
     */
    protected $viewPath = '/';

    /**
     * @var array
     */
    protected $args = [];

    /**
     * BaseController constructor.
     * @param $request
     */
    public function __construct($request)
    {
        $this->request = $request;

        $this->view = new View([]);
    }

    public function withArgs()
    {
        $this->args = func_get_args();
    }

    public function call($method)
    {
        if (method_exists($this, $method) === false)
        {
            throw new \Exception('Invalid method call : '. $method);
        }

        return $this->$method($this->args);
    }

    protected function view($view, $data = [])
    {
        $data = array_merge([
            'v' => $this->view
        ], $data);
        return $this->view->make($this->viewPath . '/' . $view, $data);
    }
}

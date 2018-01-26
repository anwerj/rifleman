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
     * @var int default response code
     */
    protected $responseStatus = 200;

    /**
     * @var array default response headers
     */
    protected $responseHeaders = [];

    /**
     * BaseController constructor.
     * @param $request
     */
    public function __construct($request)
    {
        $this->request = $request;

        $this->view = new View([]);

        $this->init();
    }

    protected function init()
    {

    }

    public function withArgs($arguments): self
    {
        $this->args = $arguments;

        return $this;
    }

    public function call($action)
    {
        $callable = $action . $this->request->getMethod();

        if (method_exists($this, $callable) === false)
        {
            throw new \Exception('Invalid action call : '. $callable);
        }

        return [$this->$callable($this->args), $this->responseStatus, $this->responseHeaders];
    }

    protected function input(string $key = null, $default = null)
    {
        $all = $this->request->request->all();
        if ($key === null)
        {
            return $all;
        }
        return array_get($all, $key, $default);
    }

    protected function arg(string $key = null, $default = null)
    {
        if ($key === null)
        {
            return $this->args;
        }

        return array_get($this->args, $key, $default);
    }

    protected function view($view, $data = [])
    {
        $data = array_merge([
            'pre' => ['v' => $this->view]
        ], $data);
        return $this->view->make($this->viewPath . '/' . $view, $data);
    }

    protected function toJson($mixed)
    {
        $this->responseHeaders['Content-Type'] = 'application/json';

        return json_encode($mixed);
    }
}

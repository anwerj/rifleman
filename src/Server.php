<?php

namespace Rifle;

use Symfony\Component\HttpFoundation;

class Server
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var int Default Status
     */
    protected $status = 200;

    /**
     * @var array Default Headers
     */
    protected $headers = [];

    public function __construct()
    {
        $this->request = HttpFoundation\Request::createFromGlobals();

        $this->handler = new Handler($this->request);
    }

    public function serve()
    {
        $method = $this->request->getMethod();

        $action = 'serve'.$method;

        try
        {
            $content = $this->$action();
        }
        catch (\Throwable $e)
        {
            list($content, $this->status) = $this->handler->handle($e);
        }

        return HttpFoundation\Response::create($content, $this->status, $this->headers)->send();
    }

    protected function serveGet()
    {
        $api = $this->request->get('api', 'page');
        $act = $this->request->get('act', 'index');

        switch ($api)
        {
            case '';
            case 'page':
                return $this->controller('page')
                            ->call($act);

            default:
                $this->handler->throwEx('Invalid API : '. $api);
        }
    }

    protected function controller($name)
    {
        $className = "\Rifle\Controllers\\" . ucfirst($name) . 'Controller';

        return new $className($this->request, $this->handler);
    }
}

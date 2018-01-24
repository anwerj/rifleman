<?php

namespace Rifle;

use Rifle\Controllers\BaseController;
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
        try
        {
            list($content, $this->status, $this->headers) = $this->serveRequest();
        }
        catch (\Exception $e)
        {
            list($content, $this->status) = $this->handler->handle($e);
        }

        //\Log::info('_LOGGER_',[$this->status]);
        return HttpFoundation\Response::create($content, $this->status, $this->headers)->send();
    }

    protected function serveRequest()
    {
        $api = $this->request->get('api', 'page');
        $act = $this->request->get('action', 'index');

        return $this->controller($api)
                    ->withArgs($this->request->query->all())
                    ->call($act);
    }

    protected function controller($name): BaseController
    {
        $className = "\Rifle\Controllers\\" . ucfirst($name) . 'Controller';

        return new $className($this->request, $this->handler);
    }
}

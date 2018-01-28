<?php

namespace Rifle\Controllers;

use Rifle\Services\Session;

class PageController extends BaseController
{
    protected $name = 'page';

    protected $viewPath = 'page';

    public function indexGet()
    {
        $sessionId = $this->arg('session_id');

        if ($sessionId === null)
        {
            $data = Session::generate($this->arg('session_count', 2));
        }
        else
        {
            $data = Session::retrieve($sessionId);
        }

        return $this->view('index', $data);
    }

    public function listGet()
    {
        $sessionId = $this->arg('session_id');

        $data = Session::retrieve($sessionId);

        foreach ($data['connections'] as $index => $connection)
        {
            $prefil = $this->arg('connections.'.$connection->id, DIRECTORY_SEPARATOR);
            $data['connections'][$index]->prefill = $prefil;
        }

        return $this->view('list', $data);
    }
}

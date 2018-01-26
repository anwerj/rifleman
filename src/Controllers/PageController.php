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
            $session = Session::generate($this->arg('session_count', 2));
        }
        else
        {
            $session = Session::retrieve($sessionId);
        }

        return $this->view('index', ['session' => $session]);
    }
}

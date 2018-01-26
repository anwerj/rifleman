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

        \Log::info('_LOGGER_',[$data]);
        return $this->view('index', $data);
    }
}

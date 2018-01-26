<?php

namespace Rifle\Controllers;

use Rifle\Services\Session;

class SessionController extends BaseController
{
    protected $name = 'session';

    protected $viewPath = 'session';

    /**
     * @var Session
     */
    protected $session;

    protected function init()
    {
        $this->session = new Session();
    }

    public function connectPost()
    {
        $response = $this->session
                         ->boot($this->input('session'), $this->input('con'))
                         ->check();

        return $this->toJson($response);
    }

    public function listGet()
    {
        $data = $this->session->retrieve($this->arg('session_id'));

        $response = $this->session
                         ->load($data['session'], $data['connections'])
                         ->list($this->arg('path'));

        return $this->toJson($response);
    }
}

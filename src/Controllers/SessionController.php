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
}

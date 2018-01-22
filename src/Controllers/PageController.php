<?php

namespace Rifle\Controllers;

class PageController extends BaseController
{
    protected $name = 'page';

    protected $viewPath = 'page';

    public function index()
    {
        return $this->view('index');
    }
}

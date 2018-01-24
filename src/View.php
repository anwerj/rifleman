<?php

namespace Rifle;

use Jenssegers\Blade\Blade;

class View
{
    /**
     * @var Blade
     */
    protected $blade;

    public function __construct(array $options)
    {
        $viewsPath  = $options['viewsPath'] ?? __DIR__ . '/../resources/views';

        $cachePath  = $options['cachePath'] ?? __DIR__ . '/../storage/cache';

        $this->blade = new Blade($viewsPath, $cachePath);
    }

    public function make(string $view, array $data)
    {
        return $this->blade->make($view, $data);
    }

    public function render(string $view, array $data)
    {
        return $this->blade->render($view, $data);
    }

    public static function route($api, $action = 'index', $data = [])
    {
        $query = http_build_query($data);

        return "/?api=$api&action=$action&$query";
    }

    public static function boot($options = [])
    {
        return new self($options);
    }
}

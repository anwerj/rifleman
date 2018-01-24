<?php

namespace Rifle\Services\Connections;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class HttpConnection extends \Rifle\Services\Connection
{
    protected $type = self::HTTP;

    protected function initialize($options)
    {
        $this->path = self::validatePath($options);

        $this->name = self::validateName($options);
    }

    public function check(): callable
    {
        return
            function(): bool
            {
                $response = $this->response('check');

                return $response->getStatusCode() === 200;
            };
    }

    public function list(string $path): callable
    {

    }

    public function file(string $path): callable
    {

    }

    public function put(string $path, string $content): callable
    {

    }

    protected function response($action, $method = 'GET', $data = [])
    {
        $client = new Client();

        $options = [
            'query' => [
                'action' => $action
            ],
            'form_params' => [
                'data'   => $data
            ]
        ];

        $response = new Response(500);

        try
        {
            $response = $client->request($method, $this->path, $options);
        }
        catch (\Exception $e)
        {
            if (method_exists($e, 'getResponse'))
            {
                $response = new Response($e->getCode(), [], $e->getMessage());
            }

            $this->log->error("Error in action : $action", [$e->getMessage(), $response->getBody()]);
        }

        return $response;
    }
}

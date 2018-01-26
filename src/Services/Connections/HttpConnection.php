<?php

namespace Rifle\Services\Connections;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Rifle\Db;

class HttpConnection extends \Rifle\Services\Connection
{
    protected $type = self::HTTP;

    protected function initialize(& $options)
    {
        $options['path'] = self::validatePath($options);

        $options['name'] = self::validateName($options);

        $options['type'] = $this->type;
    }

    public function check(): callable
    {
        return
            function(): bool
            {
                $response = $this->response('check');

                $response = $this->validateCheckStatus($response);

                $this->saveStatus($response);

                return $this->getStatus();
            };
    }

    public function list(string $path): callable
    {
        return
            function() use ($path): array
            {
                $response = $this->response('list', 'GET', ['path' => $path]);

                $response = $this->validateCheckStatus($response);

                $this->saveStatus($response);

                return $response;
            };
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
            ],
            'headers' => [
                'X-R-ID'     => $this->getId(),
                'X-R-SECRET' => $this->getSecret(),
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

            $this->log->error("Error in action : $action", [$response->getBody()->getContents()]);
        }

        return $response;
    }

    protected function validateCheckStatus($response)
    {
        $json = $response->getBody()->getContents();

        $content = json_decode($json, true);

        \Log::info('Http response body '.$this->getId(),[$content]);
        return $content;
    }
}

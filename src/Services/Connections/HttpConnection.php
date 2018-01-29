<?php

namespace Rifle\Services\Connections;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Rifle\Db;

class HttpConnection extends \Rifle\Services\Connection
{
    protected $type = self::HTTP;

    protected $action;

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
                $this->action = 'check';

                $response = $this->response();

                $this->saveStatus($response);

                return $this->getStatus();
            };
    }

    public function list(string $path): callable
    {
        return
            function() use ($path): array
            {
                $this->action = 'list';

                $response = $this->response(['path' => $path]);

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

    protected function response($queryData = [], $method = 'GET', $postData = [])
    {
        $client = new Client();

        $queryData['action'] = $this->action;

        $options = [
            'query' => $queryData,
            'form_params' => [
                'data'   => $postData
            ],
            'headers' => [
                'X-R-ID'     => $this->getId(),
                'X-R-SECRET' => $this->getSecret(),
            ]
        ];

        try
        {
            $response = $client->request($method, $this->path, $options);
            $json = $response->getBody()->getContents();
            $content = json_decode($json, true);
        }
        catch (\Exception $e)
        {
            $error = get_class($e).':'.$e->getMessage();
            if ($e->hasResponse())
            {
                $json = $e->getResponse()->getBody()->getContent();
                \Log::info('_LOGGER_',[$json]);
            }
            $content = ['success' => false, 'error'=> $error];
        }

        return $content;
    }
}

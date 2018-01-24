<?php

namespace Rifle;

use Symfony\Component\HttpFoundation;

class Handler
{
    /**
     * @var HttpFoundation\Request
     */
    protected $request;

    public function handle(\Throwable $e)
    {
         return [
            $this->getHtml($e),
            400
        ];
    }

    public function throwEx($message, $code = 400)
    {
        throw new \Exception($message);
    }

    private function getHtml(\Throwable $e)
    {
        $previous = '';
        if ($p = $e->getPrevious())
        {
            $previous .= $this->getHtml($p);
        }
        $trace = nl2br($e->getTraceAsString());
        return "<h3>{$e->getMessage()}</h3>
                <h4>{$e->getFile()} : {$e->getLine()}</h4>
                <div>{$trace}</div>
                <br>
                $previous";
    }
}

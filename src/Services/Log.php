<?php

namespace Rifle\Services;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log
{
    /**
     * @var Logger
     */
    private static $logger;

    public static function info(string $key, array $data = [])
    {
        self::logger()->info($key, $data);
    }

    public static function error(string $key, array $data = [])
    {
        self::logger()->error($key, $data);
    }

    private static function logger()
    {
        if (empty(self::$logger) === true)
        {
            self::$logger = new Logger('rifle');
            self::$logger->pushHandler(new StreamHandler(__DIR__ . '/../../storage/logs/app.log'));
        }

        return self::$logger;
    }
}

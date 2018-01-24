<?php

namespace Rifle\Services;

use Rifle\Db;
use Rifle\Services\Connections;

abstract class Connection
{
    const HTTP = 'http';

    const FILE = 'file';

    /**
     * @var string file/http/ftp/ssh
     */
    protected $type;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var
     */
    protected $sessionId;

    /**
     * @var
     */
    protected $id;

    /**
     * @var
     */
    protected $status = 'idle';

    protected $secret;

    abstract function check();
    abstract function list(string $path);
    abstract function file(string $path);
    abstract function put(string $path, string $content);

    /**
     * Connection constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->log = new Log();

        $this->id  = $options['id'];

        $this->sessionId = $options['session_id'];

        $this->secret = $options['secret'];

        $this->initialize($options);

        $this->save();
    }

    public function save()
    {
        return Db::table(DB::CONNECTION)->getOrCreate($this->toArray());
    }

    public static function boot($options)
    {
        $type = self::validateType($options);

        switch ($type)
        {
            case self::HTTP:
                $connection = new Connections\HttpConnection($options);
        }

        $connection->save();

        return $connection;
    }

    protected static function validatePath($options)
    {
        return $options['path'];
    }

    protected static function validateType($options)
    {
        return array_get($options, 'type', 'http');
    }

    protected static function validateName($options)
    {
        return array_get($options, 'name', sha1(self::validatePath($options)));
    }
    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param mixed $sessionId
     */
    public function setSessionId($sessionId): void
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param mixed $secret
     */
    public function setSecret($secret): void
    {
        $this->secret = $secret;
    }

    public function toArray()
    {
        return [
            'id'         => $this->getId(),
            'secret'     => $this->getSecret(),
            'session_id' => $this->getSessionId(),
            'type'       => $this->getType(),
            'name'       => $this->getName(),
            'path'       => $this->getPath(),
            'status'     => $this->getStatus(),
        ];
    }
}

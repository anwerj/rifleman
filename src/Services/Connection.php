<?php

namespace Rifle\Services;

use Rifle\Db;
use Rifle\DbRow;
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

    /**
     * @var mixed
     */
    protected $secret;

    /**
     * @var
     */
    protected $dbRow;

    abstract protected function initialize(& $options);
    abstract function check();
    abstract function list(string $path);
    abstract function file(string $path);
    abstract function put(string $path, string $content);

    /**
     * Connection constructor.
     * @param array $options
     */
    public function __construct($options)
    {
        $this->log = new Log();

        if ($options instanceof DbRow)
        {
            $this->dbRow = $options;
        }
        else
        {
            $this->initialize($options);
            $this->save($options, true);
        }
        $this->setFromRow();
    }

    public function save(array $options, $upsert = false)
    {
        if ($upsert === true)
        {
            $this->dbRow = Db::table(Db::CONNECTION)
                             ->secret($options)
                             ->upsert($options);
        }
        else
        {
            $this->dbRow = DB::table(Db::CONNECTION)
                             ->update($this->dbRow, $options);
        }
    }

    public function setFromRow()
    {
        $this->setId($this->dbRow->id);
        $this->setSecret($this->dbRow->secret);
        $this->setSessionId($this->dbRow->session_id);
        $this->setPath($this->dbRow->path);
        $this->setName($this->dbRow->name);
        $this->setType($this->dbRow->type);
        $this->setStatus($this->dbRow->status);
    }

    public function saveStatus($response)
    {
        $status = $response['success'] ?? false;
        if ($status === true)
        {
            $toUpdate = [
                'status'       => 'connected',
                'connected_at' => Db::timestamp(),
            ];
        }
        else
        {
            $toUpdate = [
                'status'       => 'disconnected',
                'error'        => $response['error'] ?? 'invalid response from connection'
            ];
        }

        $this->save($toUpdate);
    }

    public static function load(DbRow $connection)
    {
        switch ($connection->type)
        {
            case self::HTTP:
                $connection = new Connections\HttpConnection($connection);
        }

        return $connection;
    }

    public static function boot($options)
    {
        $type = self::validateType($options);

        switch ($type)
        {
            case self::HTTP:
                $connection = new Connections\HttpConnection($options);
        }

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
    public function setPath(string $path)
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
    public function setType(string $type)
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
    public function setName(string $name)
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
    public function setSessionId($sessionId)
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
    public function setId($id)
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
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return $this->getStatus() === 'connected';
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
    public function setSecret($secret)
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

    public function toDbRow()
    {
        return $this->dbRow;
    }
}

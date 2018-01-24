<?php

namespace Rifle;

use Medoo\Medoo;

class DbException extends \Exception
{}

class DbRow
{
    protected $table;
    protected $attributes   = [];
    protected $isCollection = false;
    protected $rows        = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function setRows(array $rows): self
    {
        $this->isCollection = true;
        foreach ($rows as $row)
        {
            $this->rows[] = (new self($this->table))->setAttributes($row);
        }
        return $this;
    }

    public function toArray()
    {
        $array = [];
        if ($this->isCollection === true)
        {
            foreach ($this->rows as $row)
            {
                $array[] = $row->toArray();
            }
        }
        else
        {
            $array = $this->attributes;
        }

        return $array;
    }

    public function __get($key)
    {
        if ($this->isCollection === true)
        {
            throw new DbException("$key can't be fetch for collection of $this->table");
        }
        if (isset($this->attributes[$key]))
        {
            return $this->attributes[$key];
        }
        throw new DbException("$key is not present in $this->table");
    }
}

class Db
{
    const SESSION    = 'session';
    const CONNECTION = 'connection';

    static $persist;

    protected $connection;

    private $table;

    private function __construct()
    {
        $this->connection = new Medoo([
            'database_type' => 'sqlite',
            'database_file' => __DIR__ . '/../resources/database/sqlite.db'
        ]);
    }

    private function setTable(string $table)
    {
        $this->table = $table;
    }

    public static function table($table): self
    {
        if (self::$persist === null)
        {
            self::$persist = new self();
        }
        self::$persist->setTable($table);

        return self::$persist;
    }

    public function insert($data)
    {
        $this->connection->insert($this->table, $data);
        $id = $this->connection->id();
        if(empty($id) === false)
        {
            return $id;
        }
        $this->throwError();
    }

    public function getById($id)
    {
        $item = $this->connection->get($this->table, '*', ['id' => $id]);
        if (empty($item) === false)
        {
            return (new DbRow($this->table))->setAttributes($item);
        }
    }

    public function getOrCreate(array $entry)
    {
        $current = $this->getById($entry['id']);
        if (empty($current))
        {
            $id = $this->insert($entry);
            $current = $this->getById($id);
        }
        return $current;
    }

    public function throwError($message = null)
    {
        $message = $message ?? implode(':', $this->connection->error());

        throw new DbException($message);
    }
}

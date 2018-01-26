<?php

namespace Rifle;

use Medoo\Medoo;
use Rifle\Services\Log;

class Db
{
    const SESSION    = 'session';
    const CONNECTION = 'connection';

    static $persist;

    protected $connection;

    private $table;

    private $ignoreOnUpdate = [];

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

    public static function timestamp($time = null)
    {
        return time();
    }

    public function insert($data)
    {
        if ($this->hasColumn('created_at'))
        {
            $data['created_at'] = Db::timestamp();
        }
        Log::debug("Inserting $this->table", $data);
        $this->connection->insert($this->table, $data);
        $id = $this->connection->id();
        if(empty($id) === false)
        {
            return $data[$this->getPrimaryKey()];
        }
        $this->throwError();
    }

    public function getByPrimaryKey($primaryKey, $strict = false)
    {
        $item = $this->connection->get($this->table, '*', [$this->getPrimaryKey() => $primaryKey]);
        if (empty($item) === false)
        {
            return (new DbRow($this->table))->setAttributes($item);
        }
        if ($strict === true)
        {
            $this->throwError("$this->table doesn't have pk : $primaryKey");
        }
    }

    public function update(DbRow $row, array $replace)
    {
        $primaryKey = $this->getPrimaryKey();

        Log::debug("Updating $this->table ". $row->{$primaryKey}, [$replace]);
        $updated = $this->connection->update($this->table, $replace, [$primaryKey => $row->{$primaryKey}]);

        if (empty($updated) === false)
        {
            return $row->setAttributes($replace);
        }
        $this->throwError();
    }

    public function upsert(array $entry): DbRow
    {
        $current = $this->getByPrimaryKey($entry[$this->getPrimaryKey()]);
        if (empty($current) === true)
        {
            $id = $this->insert($entry);
            $current = $this->getByPrimaryKey($id, true);
        }
        else
        {
            $current = $this->update($current, array_except($entry, $this->ignoreOnUpdate));
            $this->ignoreOnUpdate = [];
        }
        return $current;
    }

    public function fetch(array $filter, int $limit = null)
    {
        $items = $this->connection->select($this->table, '*', $filter);

        if (empty($items) === true)
        {
            //$this->throwError();
        }

        return (new DbRow($this->table))->setRows($items);
    }

    public function secret(& $entry)
    {
        $validity = ceil(time()/84600);

        $entry['secret'] = password_hash($entry['id'].$validity, PASSWORD_BCRYPT);

        $refresh = filter_var(array_get($entry, 'refresh_secret'), FILTER_VALIDATE_BOOLEAN);
        unset($entry['refresh_secret']);
        if (empty($refresh))
        {
            $this->ignoreOnUpdate[] = 'secret';
        }

        return $this;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function hasColumn($column)
    {
        return true;
    }

    public function throwError($message = null)
    {
        $message = $message ?? implode(':', $this->connection->error());

        throw new DbException($message);
    }
}

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
        $this->attributes = array_merge($this->attributes, $attributes);
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

    public function toArray(bool $nested = true)
    {
        $array = [];
        if ($this->isCollection === true )
        {
            foreach ($this->rows as $row)
            {
                if ($nested === true)
                {
                    $array[] = $row->toArray();
                }
                else
                {
                    $array[] = $row;
                }

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

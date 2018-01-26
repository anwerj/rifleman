<?php

namespace Rifle\Services;

use Rifle\Db;
use Rifle\DbRow;
use Rifle\View;

class Session
{
    /**
     * @var DbRow
     */
    public $session;
    /**
     * @var array
     */
    public $connections = [];

    public function load(DbRow $session, $connections = [])
    {
        $this->session = $session;

        foreach ($connections as $id => $connection)
        {
            $this->connections[$connection->id] = Connection::load($connection);
        }

        return $this;
    }

    public function boot(array $session, array $connections): self
    {
        $this->session = Db::table(Db::SESSION)->secret($session)
                                               ->upsert($session);

        foreach ($connections as $id => $connection)
        {
            $connection['id']         = $id;
            $connection['session_id'] = $this->session->id;
            $this->connections[$id]   = Connection::boot($connection);
        }

        return $this;
    }

    public function list($path)
    {
        $content = [
            'connections' => [],
        ];
        $sessionStatus = true;

        foreach ($this->connections as $id => $connection)
        {
            $connectionList = $connection->list($path)();
            $content['connections'][$id] = $connectionList['content'] ?? [];
            $sessionStatus = ($sessionStatus and $connection->isConnected());
        }

        $content['session'] = $this->handleSessionStatus($sessionStatus);

        return $content;
    }

    public function check()
    {
        $content = [
            'connections' => [],
        ];

        $sessionStatus = true;

        foreach ($this->connections as $id => $connection)
        {
            $connectionStatus = $connection->check()();
            $content['connections'][$id] = $connection->toDbRow()->toArray();
            $sessionStatus = ($sessionStatus and $connectionStatus);
        }

        $content['session'] = $this->handleSessionStatus($sessionStatus);

        return $content;
    }

    protected function handleSessionStatus($status)
    {
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
            ];
        }
        $toUpdate['updated_at'] = Db::timestamp();

        return Db::table(Db::SESSION)->update($this->session, $toUpdate)
            ->toArray();
    }

    public static function generate(int $count = 2, string $id = null)
    {
        $id = $id ?? sha1(intval(time()/84000));

        $data = [
            'session'     => Db::table(Db::SESSION)->upsert(['id' => $id]),
            'connections' => []
        ];

        for ($i = 0; $i<$count; $i++)
        {
            $connection = [
                'id'            => sha1($i. $id),
                'session_id'    => $id,
            ];
            $data['connections'][] = Db::table(Db::CONNECTION)->upsert($connection);
        }

        return $data;
    }

    public static function retrieve($id)
    {
        $data = [
            'session'       => Db::table(Db::SESSION)->getByPrimaryKey($id, true),
            'connections'   => Db::table(Db::CONNECTION)->fetch(['session_id' => $id])->toArray(false)
        ];

        return $data;
    }

}

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

        if ($sessionStatus === true)
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

        $content['session'] = Db::table(Db::SESSION)->update($this->session, $toUpdate)
                                                    ->toArray();

        return $content;
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

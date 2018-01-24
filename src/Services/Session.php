<?php

namespace Rifle\Services;

use Rifle\Db;
use Rifle\View;

class Session
{
    /**
     * @var array
     */

    public $session;
    /**
     * @var array
     */
    public $connections = [];

    public function boot(array $session, array $connections): self
    {
        $session['secret'] = $this->generateSecret($session['id']);

        $this->session = Db::table(Db::SESSION)->getOrCreate($session);

        foreach ($connections as $id => $connection)
        {
            $connection['id']         = $id;
            $connection['session_id'] = $this->session->id;
            $connection['secret']     = $this->generateSecret($id);
            $this->connections[$id]   = Connection::boot($connection);
        }

        return $this;
    }

    public function check()
    {
        $content = [];
        $view = [
            'session' => $this->session
        ];
        foreach ($this->connections as $id => $connection)
        {
            if ($connection->check()())
            {
                $content[$id] = ['success' => true, 'id' => $id];
            }
            else
            {
                $view['connection'] = $connection;
                $content[$id] = [
                    'success'    => false,
                    'id'         => $id,
                    'action'     => 'add_connector',
                    'link'       => View::route('session', 'connector'),
                    'content'    => View::boot()->render('connection.connector', $view)
                ];
            }
        }

        return $content;
    }

    private function generateSecret($id)
    {
        return password_hash($id, PASSWORD_BCRYPT);
    }

    public static function generate(int $count = 2, string $id = null)
    {
        $id = $id ?? sha1(intval(time()/84000));

        $data = [
            'id'          => $id,
            'connections' => []
        ];

        for ($i = 0; $i<$count; $i++)
        {
            $data['connections'][] = ['id' => sha1($id. $i)];
        }

        return $data;
    }

    public static function retrieve($id)
    {
        $session = Db::table(Db::SESSION)->getById($id);

        if (empty($session))
        {
            throw new \Exception("Invalid id for session : $id");
        }
    }

}

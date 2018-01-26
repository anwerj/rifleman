<?php
echo <<<STUB
<?php

\$session = [
    'id'         => '$session->id',
    'secret'     => '$session->secret',
    'connection' => [
        'id'     => '$connection->id',
        'secret' => '$connection->secret',
    ]
];
STUB;

<?php
return [
    'host' => getenv('DB_HOST') ?: '172.17.0.1',
    'dbname' => getenv('DB_NAME') ?: 'avp2_teste1',
    'user' => getenv('DB_USER') ?: 'root',
    'pass' => getenv('DB_PASS') ?: '',
    'port' => getenv('DB_PORT') ?: '3307',
];

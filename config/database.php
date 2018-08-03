<?php

return [
    'host' => '192.168.1.10',
    'user' => 'root',
    'passwd' => '',
    'prefix' => 'pr_',
    'db' => 'php-radius',
    'port' => 3306,
    'param' => [
        'charset' => 'utf8',
        PDO::ATTR_PERSISTENT => true
    ]
];
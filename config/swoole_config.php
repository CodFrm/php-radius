<?php
/**
 *============================
 * author:Farmer
 * time:2018/7/22 11:54
 * blog:blog.icodef.com
 * function:swoole的配置文件
 *============================
 */


return [
    'secret' => 'test123',
    'auth_port' => 1812,
    'account_port' => 1813,
    'db' => [
        'host' => '192.168.1.10',
        'port'=>3306,
        'database' => 'php-radius',
        'user' => 'root',
        'password' => ''
    ]
];
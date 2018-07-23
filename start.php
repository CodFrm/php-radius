<?php

require 'vendor/autoload.php';
/**
 * 加载框架核心内容
 * 进入框架入口,启动框架
 */
$app = new HuanL\Core\Application(
    realpath(__DIR__)
);
$app->run();
//需要将框架运行起来才能使swoole里的一些模型之类的跑起来

$server = new \Radius\radius(__DIR__);
$server->run();



<?php

/**
 * 引入composer的自动加载文件
 */
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * 加载框架核心内容
 * 进入框架入口,启动框架
 */
$app = new HuanL\Core\Application(
    realpath(__DIR__ . '/../')
);

/**
 * 处理并发送请求请求
 */
$app->run();


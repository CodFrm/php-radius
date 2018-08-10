<?php
/**
 *============================
 * author:Farmer
 * time:2018/8/10 14:34
 * blog:blog.icodef.com
 * function:服务器监控,使用websocket
 *============================
 */


namespace Radius;


use swoole_websocket_server;

class serverMonitor {

    /**
     * websocket服务器
     * @var swoole_websocket_server
     */
    protected $server;

    /**
     * 客户端连接信息
     * @var array
     */
    protected $client = [];

    /**
     * 开始运行,创建websocket服务器
     */
    public function run() {
        $this->server = new swoole_websocket_server("0.0.0.0", 5135);
        $this->server->on('open', function (swoole_websocket_server $server, $request) {
            //客户端进入就将客户存入$client数组中

        });
        $this->server->start();
    }
}
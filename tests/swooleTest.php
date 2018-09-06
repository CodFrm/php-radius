<?php
/**
 *============================
 * author:Farmer
 * time:18-9-6 上午11:29
 * blog:blog.icodef.com
 * function:测试swoole
 *============================
 */
require 'vendor/autoload.php';
/**
 * 加载框架核心内容
 * 进入框架入口,启动框架
 */
$app = new HuanL\Core\Application(
    realpath(__DIR__ . '/..')
);

define('SWOOLE_CPU_NUM', 2);
$server = new swoole_server('0.0.0.0', 8987, SWOOLE_BASE, SWOOLE_SOCK_TCP);
$server->set([
    'worker_num' => SWOOLE_CPU_NUM * 2,
    'backlog' => 128,
    'max_request' => 50,
    'dispatch_mode' => 1,
    'task_worker_num' => SWOOLE_CPU_NUM * 20,
    'task_max_request' => 20,
    'daemonize' => 0,
    'log_level' => 5
]);

$server->on('WorkerStart', function (swoole_server $serv, $work_id) {
    if ($work_id == 2) {
        $serv->tick(1000, function () {
            $c = new swoole_client(SWOOLE_SOCK_TCP);
            $c->connect('127.0.0.1', 8987);
            $c->send('test');
        });
    }
});

$server->on('Receive', function (swoole_server $server, int $fd, int $reactor_id, string $data) {
    echo $reactor_id;
    print_r(\HuanL\Core\Facade\Db::table('config')->where('config_key', 'test')->find());
});

$server->on('Task', function (swoole_server $serv, $task_id, $from_id, $data) {

});

$server->on('Finish', function (swoole_server $serv, $task_id, $data) {
//    echo $work_id . "\n";
});

class a {
    public $a = 0;
}

//服务器启动,线程堵塞...
$server->start();
<?php

require 'vendor/autoload.php';
/**
 * 加载框架核心内容
 * 进入框架入口,启动框架
 */
$app = new HuanL\Core\Application(
    realpath(__DIR__)
);
//需要将框架运行起来才能使swoole里的一些模型之类的跑起来
$pid = [];
if (in_array('radius', $argv)) {
//开启两个进程,一个radius的,一个websocket监控服务器的
    $radiusProcess = new swoole_process(function (swoole_process $worker) {
        $server = new \Radius\radius(__DIR__);
        $server->run();
    });
    if (($pid['radius'] = $radiusProcess->start()) == false) {
        exit('radius failed to open');
    }
}
if (in_array('monitor', $argv)) {
    $serverMonitorProcess = new swoole_process(function (swoole_process $worker) {
        $server = new \Radius\serverMonitor();
        $server->run();
    });
    if (($pid['monitor'] = $serverMonitorProcess->start()) == false) {
        exit('server monitor failed to open');
    }
}
//等待子进程,如果异常结束就重启
while (1) {
    $ret = swoole_process::wait();
    if ($ret) {
        $key = array_search($ret['pid'], $pid);
        if ($key !== false) {
            switch ($key) {
                case 'radius':
                    {
//                        $pid['radius'] = $radiusProcess->start();
                        echo "radius abnormal exit\n";
                        break;
                    }
                case 'monitor':
                    {
//                        $pid['monitor'] = $serverMonitorProcess->start();
                        echo "monitor abnormal exit\n";
                        break;
                    }
                default:
                    {
                        exit('abnormal pid is ended');
                    }
            }
        }
    } else {
        break;
    }
}
exit('end of program');
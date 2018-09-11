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
use swoole_server;
use swoole_websocket_frame;
use swoole_http_request;
use swoole_http_response;

class serverMonitor {

    /**
     * websocket服务器
     * @var swoole_websocket_server
     */
    protected $server;

    /**
     * 服务器的信息
     * @var array
     */
    protected $sys_msg = [];

    /**
     * 客户端连接信息
     * @var array
     */
    protected $client = [];

    function __construct() {
        $this->sys_msg['cpu'] = $this->getCpuInfo();
        $this->sys_msg['mem'] = $this->getMemInfo();
        $this->getCpuRealTimeUse();
    }

    /**
     * 开始运行,创建websocket服务器
     */
    public function run() {
        $this->server = new swoole_websocket_server("0.0.0.0", 5135);

        $this->server->on('handshake', [$this, 'onHandShake']);

        $this->server->on('message', function (swoole_server $server, swoole_websocket_frame $frame) {
            //必须写,但是我又不在这里实现什么- -
            print_r($frame->data);
        });

        $this->server->on('close', function (swoole_server $server, int $fd) {
            unset($this->client[$fd]);
        });

        $this->server->on('WorkerStart', function () {
            $this->server->tick(2000, function () {
                if (count($this->client) <= 0) return;
                //获取服务器信息
                $sendData = json_encode($this->getServerMsg());
                //定时推送服务器消息
                foreach ($this->client as $value) {
                    $this->server->push($value, $sendData);
                }
            });
        });
        $this->server->start();
    }


    /**
     * 获取服务器信息
     * @return array
     */
    protected function getServerMsg(): array {
        $ret = $this->sys_msg;
        $ret['cpu']['use'] = $this->getCpuRealTimeUse();
        $ret['disk'] = $this->getDiskInfo();
        $ret['mem'] = $this->getMemInfo();
        $ret['network'] = $this->getNetworkInfo();
        $ret['load'] = $this->getSysLoad();
        $ret['time'] = time();
        return $ret;
    }

    /**
     * 获取cpu实时使用率
     * @return int
     */
    public function getCpuRealTimeUse(): int {
        static $record = ['all' => 0, 3 => 0];
        $stat = file_get_contents("/proc/stat");
        if (preg_match('/cpu\s{0,}(.*?)[\r\n]/', $stat, $match)) {
            $info = explode(' ', $match[1]);
            $all = $info[0] + $info[1] + $info[2] + $info[3] + $info[4] + $info[5] + $info[6];
            $ret = ($all - $record['all'] - ($info[3] - $record[3])) / ($all - $record['all']) * 100;
            $record = $info;
            $record['all'] = $all;
            var_dump($ret);
            if ($ret <= 0)
                return 0;
            return $ret;
        }
        return 0;
    }

    /**
     * 获取cpu负载
     * @return array
     */
    public function getSysLoad(): array {
        $info = file_get_contents("/proc/loadavg");
        return explode(' ', $info);
    }

    /**
     * 获取cpu信息
     * @return array
     */
    public function getCpuInfo(): array {
        $ret = [];
        $info = file_get_contents('/proc/cpuinfo');
        if (preg_match_all('/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(\@.-]+)([\r\n]+)/', $info, $matches)) {
            $ret['model'] = $matches[1][0];
            $ret['num'] = count($matches[1]);
        }
        return $ret;
    }

    /**
     * 获取网络信息
     * @return array
     */
    public function getNetworkInfo(): array {
        $ret = [];
        $info = file_get_contents('/proc/net/dev');
        preg_match_all('/(\w+):(.*?)[\r\n]/', $info, $match);
        foreach ($match[1] as $key => $val) {
            preg_match_all('/(\d+)\s{0,}/', $match[2][$key], $sub_match);
            $ret[$val]['out'] = intval($sub_match[1][8]);
            $ret[$val]['in'] = intval($sub_match[1][0]);
        }
        return $ret;
    }

    /**
     * 获取内存信息
     * @return array
     */
    public function getMemInfo(): array {
        $ret = ['total' => 0, 'free' => 0];
        $info = file_get_contents('/proc/meminfo');
        if (preg_match('/MemTotal:\s{0,}(\d+)\s{0,}.*?[\r\n]+/', $info, $matches)) {
            $ret['total'] = intval($matches[1]);
        }
        if (preg_match('/MemFree:\s{0,}(\d+)\s{0,}.*?[\r\n]+/', $info, $matches)) {
            $ret['free'] = intval($matches[1]);
        }
        $ret['use'] = $ret['total'] - $ret['free'];
        return $ret;
    }

    /**
     * 获取磁盘信息
     * @return array
     */
    public function getDiskInfo(): array {
        $ret = [];
        $ret['total'] = disk_total_space('.');
        $ret['use'] = $ret['total'] - disk_free_space('.');
        return $ret;
    }

    public function onHandShake(swoole_http_request $request, swoole_http_response $response) {
        //验证key
        if ($request->get['key'] != 'xiajibada') {
            $response->end();
            return false;
        }
        $secWebSocketKey = $request->header['sec-websocket-key'];
        $patten = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';
        if (0 === preg_match($patten, $secWebSocketKey) || 16 !== strlen(base64_decode($secWebSocketKey))) {
            $response->end();
            return false;
        }
        echo $request->header['sec-websocket-key'];
        $key = base64_encode(sha1(
            $request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',
            true
        ));
        $headers = [
            'Upgrade' => 'websocket',
            'Connection' => 'Upgrade',
            'Sec-WebSocket-Accept' => $key,
            'Sec-WebSocket-Version' => '13',
        ];
        if (isset($request->header['sec-websocket-protocol'])) {
            $headers['Sec-WebSocket-Protocol'] = $request->header['sec-websocket-protocol'];
        }
        foreach ($headers as $key => $val) {
            $response->header($key, $val);
        }
        $response->status(101);
        $response->end();
        //客户端进入就将客户存入$client数组中
        $this->client[$request->fd] = $request->fd;
        return true;
    }

}
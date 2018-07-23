<?php
/**
 *============================
 * author:Farmer
 * time:2018/7/22 11:59
 * blog:blog.icodef.com
 * function:
 *============================
 */


namespace Radius;

use App\Model\LoginVerifyModel;
use App\Model\UserModel;
use HuanL\Db\Driver\MySQL\MySQLDBConnect;
use HuanL\Db\SQLDb;
use \swoole_server;

class radius {
    /**
     * @var swoole_server
     */
    public $server;

    /**
     * 密钥
     * @var string
     */
    public $secret_key;

    /**
     * 配置
     * @var array
     */
    public $config = [];

    /**
     * 数据库连接对象
     * @var MySQLDBConnect
     */
    public $dbConnect = null;


    public function __construct(string $path) {
        $configPath = $path . '/config/swoole_config.php';
        $this->config = include $configPath;
        $this->dbConnect = new MySQLDBConnect($this->config['db']['user'],
            $this->config['db']['passwd'], $this->config['db']['db']
            , $this->config['db']['prefix'], $this->config['db']['host'], $this->config['db']['port'], $this->config['db']['param']
        );
    }

    /**
     * 获取一个数据库操作实例
     * @return SQLDb
     */
    public function Db($table, $alias = '') {
        $db = new SQLDb($this->dbConnect);
        return $db->table($table, $alias);
    }

    public static $ATTR_TYPE = [
        1 => 'User-Name', 2 => 'User-Password', 3 => 'CHAP-Password', 4 => 'NAS-IP-Address', 5 => 'NAS-Port',
        6 => 'Service-Type', 7 => 'Framed-Protocol', 8 => 'Framed-IP-Address', 9 => 'Framed-IP-Netmask',
        10 => 'Framed-Routing', 11 => 'Filter-Id', 12 => 'Framed-MTU', 13 => 'Framed-Compression',
        14 => 'Login-IP-Host', 15 => 'Login-Service', 16 => 'Login-TCP-Port', 17 => '(unassigned)',
        18 => 'Reply-Message', 19 => 'Callback-Number', 20 => 'Callback-Id', 21 => '(unassigned)',
        22 => 'Framed-Route', 23 => 'Framed-IPX-Network', 24 => 'State', 25 => 'Class', 26 => 'Vendor-Specific',
        27 => 'Session-Timeout', 28 => 'Idle-Timeout', 29 => 'Termination-Action', 30 => 'Called-Station-Id',
        31 => 'Calling-Station-Id', 32 => 'NAS-Identifier', 33 => 'Proxy-State', 34 => 'Login-LAT-Service',
        35 => 'Login-LAT-Node', 36 => 'Login-LAT-Group', 37 => 'Framed-AppleTalk-Link',
        38 => 'Framed-AppleTalk-Network', 39 => 'Framed-AppleTalk-Zone', 40 => 'Acct-Status-Type',
        41 => 'Acct-Delay-Time', 42 => 'Acct-Input-Octets', 43 => 'Acct-Output-Octets', 44 => 'Acct-Session-Id',
        45 => 'Acct-Authentic', 46 => 'Acct-Session-Time', 47 => 'Acct-Input-Packets', 48 => 'Acct-Output-Packets',
        49 => 'Acct-Terminate-Cause', 50 => 'Acct-Multi-Session-Id', 51 => 'Acct-Link-Count',
        60 => 'CHAP-Challenge', 61 => 'NAS-Port-Type', 62 => 'Port-Limit', 63 => 'Login-LAT-Port'];

    /**
     * 收到udp数据包
     * @param swoole_server $serv
     * @param string $data
     * @param array $clientInfo
     */
    public function onPacket(swoole_server $serv, string $data, array $clientInfo) {
        $attr = [];
        $struct = $this->unpack($data, $attr);
        $this->log('连接进入...');
        if (!(isset($struct['code']) && isset($struct['identifier']) && isset($struct['authenticator']))) {
            return;
        }
        $code = 0;
        switch ($struct['code']) {
            case 1:
                {
                    //Access-Request 接收到请求,需要处理账号密码信息,然后返回
                    $this->log("Access-Request 认证请求");
                    $code = $this->authUser($attr, $struct['authenticator']);
                    $this->log("Access-Request Req:code:$code");
                    break;
                }
            case 4:
                {
                    //Accounting-Request 计费请求
                    $this->log("Access-Request 认证请求");

                    break;
                }
            case 5:
                {
                    //Accounting-Response
                    break;
                }
            default:
                {
                    return;
                }
        }
        //接收到了信息,从数据库验证账号信息,需要判断密码是什么类型
        print_r($clientInfo);
        var_dump($serv->sendto($clientInfo['address'], $clientInfo['port'],
            $this->pack($code, $struct['identifier'], $struct['authenticator']), $clientInfo['server_socket']
        ));
        return;
    }

    public function log($msg) {
        echo "$msg\t" . date('Y/m/d H:i:s') . "\n";
    }

    /**
     * 验证账号
     * @param array $attr
     * @return int
     */
    public function authUser(array $attr, $Authenticator): int {
        if (isset($attr[static::$ATTR_TYPE[1]])) {
            //有账号密码
            if (isset($attr[static::$ATTR_TYPE[2]])) {
                //User-Password
                $passwd = $this->decode_pap_passwd($attr[static::$ATTR_TYPE[2]], $Authenticator);
                $vmodel = new LoginVerifyModel(['user' => $attr[static::$ATTR_TYPE[1]], 'passwd' => $passwd]);
                if ($vmodel->__check()) {
                    $umodel = new UserModel();
                    if ($umodel->login($vmodel) == '') {
                        return 2;
                    }
                }
                return 3;
            } else if (isset($attr[static::$ATTR_TYPE[3]])) {
                //CHAP-Password,密码必须明文储存,暂时放着,需要支持Access-Challeng
                return 3;
            } else {
                return 3;
            }
        }
        return 3;
    }

    /**
     * 对chap密码验证正确性
     * @param string $bin
     * @param string $pwd
     * @return bool
     */
    public function verify_chap_passwd(string $bin, string $pwd, string $chap): bool {
        if (strlen($bin) != 17) return false;
        $chapid = $bin[0];
        $string = substr($bin, 1);
        return md5($chapid . $pwd . $chap, true) == $string;
    }

    /**
     * 解码pap密码
     * @param string $bin
     * @param string $Authenticator
     * @return string
     */
    public function decode_pap_passwd(string $bin, string $Authenticator): string {
        $passwd = '';
        $S = $this->secret_key;
        $len = strlen($bin);
        //b1 = MD5(S + RA)
        $hash_b = md5($S . $Authenticator, true);
        for ($offset = 0; $offset < $len; $offset += 16) {
            //每次拿16字符进行解码
            for ($i = 0; $i < 16; $i++) {
                $pi = ord($bin[$offset + $i]);
                $bi = ord($hash_b[$i]);
                //c(i) = pi xor bi
                $chr = chr($pi ^ $bi);
                if ($chr == "\x0") {
                    //文本标志\x0结尾
                    return $passwd;
                }
                $passwd .= $chr;
            }
            //判断一下是不是已经结束了,然后返回
            if ($len == $offset + 16) {
                return $passwd;
            }
            //bi = MD5(S + c(i-1))
            $hash_b = md5($S . substr($bin, $offset, 16), true);
        }
        //都循环完了,还没看见结束,返回空
        return '';
    }

    /**
     * 封包
     * @param int $code
     * @param int $identifier
     * @param string $reqAuthenticator
     * @param array $attr
     * @return string
     */
    public function pack(int $code, int $identifier, string $reqAuthenticator, array $attr = []): string {
        $attr_bin = '';
        foreach ($attr as $key => $value) {
            $attr_bin .= $this->pack_attr($key, $value);
        }
        $len = 20 + strlen($attr_bin);
        //MD5(Code+ID+Length+RequestAuth+Attributes+Secret)
        $send = pack('ccna16',
                $code, $identifier, $len,
                md5(chr($code) . chr($identifier) . pack('n', $len) .
                    $reqAuthenticator . $attr_bin . $this->secret_key, true)
            ) . $attr_bin;
        return $send;
    }

    /**
     * 封包属性
     * @param  $code
     * @param string $data
     * @return string
     */
    public function pack_attr($code, string $data): string {
        return pack('cc', $code, 2 + strlen($data)) . $data;
    }

    /**
     * 解码radius数据包
     * @param string $bin
     * @param array $attr
     * @return array|bool
     */
    public function unpack(string $bin, array &$attr): array {
        //一个正常的radius封包长度是绝对大于等于20的
        if (strlen($bin) < 20) {
            return [];
        }
        //解包
        $radius = unpack('ccode/cidentifier/nlength/a16authenticator', $bin);
        //获取后面的属性长度,并且对数据包进行验证
        if (strlen($bin) != $radius['length']) {
            return [];
        }
        $attr_len = $radius['length'] - 20;
        //处理得到后面的Attributes,并且解包
        $attr = $this->unpack_attr(substr($bin, 20, $attr_len));
        if ($attr == []) {
            return [];
        }
        return $radius;
    }

    /**
     * 处理Attributes
     * @param string $bin
     * @return array
     */
    public function unpack_attr(string $bin): array {
        $attr = [];
        $offset = 0;
        $len = strlen($bin);
        while ($offset < $len) {
            $attr_type = ord($bin[$offset]);//属性类型
            $attr_len = ord($bin[$offset + 1]);//属性长度
            $attr[static::$ATTR_TYPE[$attr_type]] = substr($bin, $offset + 2, $attr_len - 2);//属性值
            //跳到下一个
            $offset += $attr_len;
        }
        //判断offset和$len是否相等,不相等认为无效,抛弃这个封包
        if ($offset != $len) {
            return [];
        }
        return $attr;
    }


    /**
     * 运行服务器
     * @param int $authPort
     * @param int $accountPort
     */
    public function run() {
        $server = new swoole_server('0.0.0.0', $this->config['auth_port'], SWOOLE_BASE, SWOOLE_SOCK_UDP);
        $server->addListener('0.0.0.0', $this->config['account_port'], SWOOLE_SOCK_UDP);
        $server->on('Packet', array($this, 'onPacket'));
        $this->server = $server;
        $this->secret_key = $this->config['secret'];
        //服务器启动,线程堵塞...
        $server->start();
    }
}

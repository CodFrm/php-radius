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

use App\Controller\AuthController;
use App\Model\AccountModel;
use App\Model\ConfigModel;
use App\Model\LoginVerifyModel;
use App\Model\ServerModel;
use App\Model\UserGroupModel;
use App\Model\UserModel;
use \swoole_server;

class radius {
    /**
     * @var swoole_server
     */
    public $server;

    /**
     * 配置
     * @var array
     */
    public $config = [];

    /**
     * 权限id
     */
    public const authId = 3;

    /**
     * 服务器缓存
     * @var array
     */
    public $serverCache = [];


    public function __construct(string $path) {
        $configPath = $path . '/config/swoole_config.php';
        $this->config = include $configPath;
        $configModel = new ConfigModel();
        $this->serverCache['online_num'] = $configModel->getConfigVal('online_num');
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
     * 二进制转换为ip字符串
     * @param string $bin
     * @return string
     */
    public static function bin2ip(string $bin): string {
        return ord(substr($bin, 0, 1)) . '.' .
            ord(substr($bin, 1, 1)) . '.' .
            ord(substr($bin, 2, 1)) . '.' .
            ord(substr($bin, 3, 1));
    }

    /**
     * 收到udp数据包
     * @param swoole_server $serv
     * @param string $data
     * @param array $clientInfo
     */
    public function onPacket(swoole_server $serv, string $data, array $clientInfo) {
        $attr = [];
        $struct = $this->unpack($data, $attr);
        if (!(isset($struct['code']) && isset($struct['identifier']) && isset($struct['authenticator']))) {
            return;
        }
        $code = 0;
        if ($server = $this->verifyServer($attr, $clientInfo['address'])) {
            switch ($struct['code']) {
                case 1:
                    {
                        //Access-Request 接收到请求,需要处理账号密码信息,然后返回
                        $this->log("Access-Request 认证请求", $clientInfo);
                        $code = $this->authUser($server, $attr, $struct['authenticator']);
                        break;
                    }
                case 4:
                    {
                        //Accounting-Request 计费请求
                        $this->log("Accounting-Request 计费请求", $clientInfo);
                        $code = $this->account($attr, $server);
                        break;
                    }
                default:
                    {
                        $this->log('错误请求', $clientInfo);
                        return;
                    }
            }
            $this->log("Req code:$code", $clientInfo);
        } else {
            $code = 3;
            $this->log("服务器验证未通过", $clientInfo);
        }
        $serv->sendto($clientInfo['address'], $clientInfo['port'],
            $this->pack($server['secret'], $code, $struct['identifier'], $struct['authenticator']), $clientInfo['server_socket']
        );
        return;
    }

    /**
     * 验证服务器是否正确/可用
     * @param array $attr
     * @param string $sourceIp
     * @return array|bool
     */
    public function verifyServer(array $attr, string $sourceIp) {
        if (!(isset($attr['NAS-IP-Address']) && isset($attr['NAS-Identifier']) && isset($attr['Acct-Session-Id']))) {
            return false;
        }
        if (self::bin2ip($attr['NAS-IP-Address']) != $sourceIp) {
            return false;
        }
//        if (isset($this->serverCache[$sourceIp . $attr['NAS-Identifier']]) &&
//            $this->serverCache[$sourceIp . $attr['NAS-Identifier']]['status'] == 0 &&
//            $this->serverCache[$sourceIp . $attr['NAS-Identifier']]['time'] + 60 > time()
//        ) {
//            $secret = $this->serverCache[$sourceIp . $attr['NAS-Identifier']]['secret'];
//            return true;
//        }
        if ($row = ServerModel::exist(['name' => $attr['NAS-Identifier'], 'ip' => $sourceIp])) {
            if ($row['status'] == 0) {
//                $secret = $row['secret'];
//                $this->serverCache[$sourceIp . $attr['NAS-Identifier']] = $row;
//                $this->serverCache[$sourceIp . $attr['NAS-Identifier']]['secret'] = $secret;
//                $this->serverCache[$sourceIp . $attr['NAS-Identifier']]['time'] = time();
                return $row;
            }
        }
        return false;
    }


    /**
     * 计费
     * @param array $attr
     * @return int
     */
    public function account(array $attr, array $server): int {
        $account_row = 0;
        if (!$account_row = $this->verifyAccount($attr, $server)) {
            return 3;
        }
        //验证时间等信息
        if ($account_row['end_time'] != 0) {
            return 3;
        }
        if (isset($attr['Acct-Status-Type'])) {
            $atype = unpack('Nast', $attr['Acct-Status-Type']);
            $accountModel = new AccountModel();
            switch ($atype['ast']) {
                case 1:
                    {
                        //只允许10秒的延期
                        if ($account_row['beg_time'] + 10 < time()) {
                            return 3;
                        }
                        //开始计费,更新数据库中的记录
                        $client_ip = '';
                        if (isset($attr['Framed-IP-Address'])) {
                            $client_ip = self::bin2ip($attr['Framed-IP-Address']);
                        }
                        $accountModel->updateAccount($account_row['account_id'], [
                            'client_ip' => $client_ip
                        ]);
                        $accountModel->deleteInvalidRecord();
                        return 5;
                    }
                case 2:
                    {
                        //结束计费
                        $data = ['end_time' => time()];
                        if (isset($attr['Acct-Input-Octets']) && isset($attr['Acct-Output-Octets'])) {
                            $tmp = unpack('Naio', $attr['Acct-Input-Octets']);
                            $data['input_octets'] = $tmp['aio'];
                            $tmp = unpack('Naoo', $attr['Acct-Output-Octets']);
                            $data['output_octets'] = $tmp['aoo'];
                        }
                        $accountModel->updateAccount($account_row['account_id'], $data);
                        return 5;
                    }
            }
        }
        return 3;
    }

    /**
     * 验证计费
     * @param array $attr
     * @param array $server
     * @return array|int
     */
    public function verifyAccount(array $attr, array $server) {
        $accountModel = new AccountModel();
        $userModel = new UserModel();
        $user = [];
        if (!(isset($attr['User-Name']) && $user = $userModel->user2msg($attr['User-Name']))) {
            return 0;
        }
        //对session验证,之前需要获取到用户的信息
        if (!$row = $accountModel->verifySession($user['uid'], $attr['Acct-Session-Id'], $server['server_id'])) {
            return 0;
        }
        return $row;
    }

    /**
     * 输出日志
     * @param $msg
     */
    public function log($msg, $client = ['address' => '']) {
        echo "$msg\tip:{$client['address']}\t" . date('Y/m/d H:i:s') . "\n";
    }

    /**
     * 验证账号
     * @param array $server
     * @param array $attr
     * @param $Authenticator
     * @return int
     */
    public function authUser(array $server, array $attr, $Authenticator): int {
        if (isset($attr['User-Name'])) {
            //有账号密码
            if (isset($attr['User-Password'])) {
                //User-Password
                $passwd = $this->decode_pap_passwd($attr['User-Password'], $Authenticator, $server['secret']);
                $vmodel = new LoginVerifyModel(['user' => $attr['User-Name'], 'passwd' => $passwd]);
                if ($vmodel->__check()) {
                    $umodel = new UserModel();
                    if ($umodel->login($vmodel, $row) == '') {
                        if ($row['status'] != 0) {
                            //非正常状态
                            return 3;
                        }
                        //对权限进行验证
                        $ugmodel = new UserGroupModel();
                        $success = AuthController::auth(static::authId, $ugmodel->getUserGroup($row['uid']));
                        if ($success) {
                            //验证成功,在数据库计费表中增加一条记录
                            $accountModel = new AccountModel();
                            //验证在线个数
                            if ($accountModel->onlineNumber($row['uid']) >= $this->serverCache['online_num']) {
                                return 3;
                            }
                            if (!$accountModel->addAccount([
                                'session' => $attr['Acct-Session-Id'], 'uid' => $row['uid'],
                                'server_id' => $server['server_id'], 'name' => $server['name'], 'ip' => $server['ip'],
                                'beg_time' => time()
                            ])) {
                                return 3;
                            }
                            return 2;
                        }
                    }
                }
            } else if (isset($attr['CHAP-Password'])) {
                //CHAP-Password,CHAP验证密码必须明文储存,暂时放着,需要支持Access-Challeng
                //对于现在的OpenVpn没打算实现这个,也不需要实现
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
     * @param string $secret
     * @return string
     */
    public function decode_pap_passwd(string $bin, string $Authenticator, string $secret): string {
        $passwd = '';
        $S = $secret;
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
    public function pack(string $secret, int $code, int $identifier, string $reqAuthenticator, array $attr = []): string {
        $attr_bin = '';
        foreach ($attr as $key => $value) {
            $attr_bin .= $this->pack_attr($key, $value);
        }
        $len = 20 + strlen($attr_bin);
        //MD5(Code+ID+Length+RequestAuth+Attributes+Secret)
        $send = pack('ccna16',
                $code, $identifier, $len,
                md5(chr($code) . chr($identifier) . pack('n', $len) .
                    $reqAuthenticator . $attr_bin . $secret, true)
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
            if (isset(static::$ATTR_TYPE[$attr_type])) {
                $attr[static::$ATTR_TYPE[$attr_type]] = substr($bin, $offset + 2, $attr_len - 2);//属性值
            } else {
                $this->log("未知的属性:$attr_type 值:" . substr($bin, $offset + 2, $attr_len - 2));
            }
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
        //服务器启动,线程堵塞...
        $server->start();
    }
}

<?php


namespace App\Model;


use App\Model\Admin\AddUserVerifyModel;
use App\Model\Admin\UpdateUserVerifyModel;
use HuanL\Core\App\Model\DbModel;
use HuanL\Core\Facade\Db;
use HuanL\Request\Request;

class UserModel extends DbModel {

    public const table = 'users';

    public const primaryKey = 'uid';

    public function __construct() {
        parent::__construct();
    }

    /**
     * 注册账号,返回uid,0为注册失败
     * @param RegisterVerifyModel $registerVerifyModel
     * @return int
     */
    public function register(RegisterVerifyModel $registerVerifyModel): int {
        /** @var Request $req */
        $req = app(Request::class);
        if ($this->db()->insert([
                'user' => $registerVerifyModel->user,
                'passwd' => 'null',
                'email' => $registerVerifyModel->email,
                'reg_ip' => $req->getip(),
                'reg_time' => time()
            ]) <= 0) {
            return 0;
        }
        $lastId = $this->db()->lastId();
        if (!$this->db()->where(['uid' => $lastId])->update([
            'passwd' => $this->passwdEncode($lastId, $registerVerifyModel->user, $registerVerifyModel->passwd)
        ])) {
            return 0;
        }
        return $lastId;
    }

    /**
     * 用户登录
     * @param LoginVerifyModel $loginVerifyModel
     * @return string
     */
    public function login(LoginVerifyModel $loginVerifyModel, &$row = []): string {
        $row = $this->db()->where(['user' => $loginVerifyModel->user])->_or()
            ->where(['email' => $loginVerifyModel->user])->_or()->where(['uid' => $loginVerifyModel->user])
            ->find();
        if ($row) {
            if ($this->passwdEncode($row['uid'], $row['user'], $loginVerifyModel->passwd) == $row['passwd']) {
                $this->db()->where(['uid' => $row['uid']])->update(['last_login_time' => time()]);
                $this->cacheValue = $row;
                return '';
            }
            return '密码错误';
        }
        return '用户不存在';
    }

    /**
     * 密码加密
     * @param int $uid
     * @param string $user
     * @param string $passwd
     * @return string
     */
    public function passwdEncode(int $uid, string $user, string $passwd): string {
        return hash('sha256', $uid . $user . $passwd);
    }

    /**
     * 添加新用户,添加成功返回uid
     * @param AddUserVerifyModel $addUserVerifyModel
     * @return int
     */
    public function addNewUser(AddUserVerifyModel $addUserVerifyModel): int {
        $register = new RegisterVerifyModel();
        $register->user = $addUserVerifyModel->user;
        $register->passwd = $addUserVerifyModel->passwd;
        $register->email = $addUserVerifyModel->email;
        return $this->register($register);
    }

    /**
     * 更新用户
     * @param UpdateUserVerifyModel $updateUserVerifyModel
     * @return int
     */
    public function updateUser(UpdateUserVerifyModel $updateUserVerifyModel): int {
        $data = [
            'email' => $updateUserVerifyModel->email
        ];
        if (!empty($updateUserVerifyModel->passwd)) {
            $data['passwd'] = $this->passwdEncode($updateUserVerifyModel->uid, $updateUserVerifyModel->user['user'], $updateUserVerifyModel->passwd);
        }
        return $this->db()->where('uid', $updateUserVerifyModel->uid)->update($data);
    }

    /**
     * 修改用户状态
     * @param int $uid
     * @param int $status
     * @return int
     */
    public function updateStatus(int $uid, int $status): int {
        if (!in_array($status, [0, 1])) {
            return 0;
        }
        return $this->db()->where('uid', $uid)->update(
            ['status' => $status]
        );
    }

    /**
     * 获取用户状态
     * @param int $uid
     * @return int
     */
    public function getUserState(int $uid): int {
        $row = $this->db()->where('uid', $uid)->find();
        if ($row) {
            return $row['status'];
        }
        return -1;
    }

    /**
     * 获取ip最后一个注册的
     * @param $ip
     * @return array|bool
     */
    public static function getIpLastReg($ip) {
        $row = Db::table(static::table)->where('reg_ip', $ip)->order('reg_time')->find();
        return $row;
    }

    /**
     * 通过uid/用户名/邮箱获取一个用户信息
     * @param $user
     * @return array|bool
     */
    public function user2msg($user) {
        return $this->db()->where('uid', $user)->_or()->where('user', $user)->_or()->where('email', $user)->find();
    }
}

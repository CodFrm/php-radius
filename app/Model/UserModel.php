<?php


namespace App\Model;


use App\Model\Admin\AddUserVerifyModel;
use HuanL\Core\App\Model\DbModel;

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
        if ($this->db()->insert([
                'user' => $registerVerifyModel->user,
                'passwd' => 'null',
                'email' => $registerVerifyModel->email,
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
        $row = $this->db()->where(['user' => $loginVerifyModel->user])->_or()->where(['uid' => $loginVerifyModel->user])->find();
        if ($row) {
            if ($this->passwdEncode($row['uid'], $row['user'], $loginVerifyModel->passwd) == $row['passwd']) {
                $this->db()->where(['uid' => $row['uid']])->update(['last_login_time' => time()]);
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
}

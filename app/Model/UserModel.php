<?php


namespace App\Model;


use HuanL\Core\App\Model\DbModel;

class UserModel extends DbModel {

    const table = 'users';

    const primaryKey = 'uid';

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
                'email' => $registerVerifyModel->email
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
    public function login(LoginVerifyModel $loginVerifyModel): string {
        $row = $this->db()->where(['user' => $loginVerifyModel->user])->_or()->where(['uid' => $loginVerifyModel->user])->find();
        if ($row) {
            if ($this->passwdEncode($row['uid'], $row['user'], $loginVerifyModel->passwd) == $row['passwd']) {
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

}

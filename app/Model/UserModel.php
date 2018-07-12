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
     * @param $user
     * @param $passwd
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
            'passwd' => hash('sha256', $lastId . $registerVerifyModel->user . $registerVerifyModel->passwd)
        ])) {
            return 0;
        }
        return $lastId;
    }

    public function authVerify() {

    }


}

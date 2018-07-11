<?php


namespace App\Model;


use HuanL\Core\App\Model\DbModel;

class UserModel extends DbModel {

    protected $table = 'users';

    protected $primaryKey = 'uid';

    public function __construct() {
        parent::__construct('users');
    }


    /**
     * 注册账号,返回uid,0为注册失败
     * @param $user
     * @param $passwd
     * @return int
     */
    public function register(string $user, string $passwd): int {
        if ($this->db()->insert([
                'username' => $user,
                'passwd' => 'null'
            ]) <= 0) {
            return 0;
        }
        $lastId = $this->db()->lastId();
        $this->db()->where(['uid' => $lastId])->update([
            'passwd' => hash('sha256', $lastId . $user . $passwd)
        ]);
        return $lastId;
    }

    public function authVerify() {

    }


}
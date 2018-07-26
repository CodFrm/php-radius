<?php
/**
 *============================
 * author:Farmer
 * time:2018/7/26 11:38
 * blog:blog.icodef.com
 * function:
 *============================
 */


namespace App\Model;


use HuanL\Core\App\Model\DbModel;

class AccountModel extends DbModel {

    /**
     * 操作的表
     * @var string
     */
    public const table = 'account';

    /**
     * 主键字段
     * @var string
     */
    public const primaryKey = 'account_id';

    /**
     * 添加一条计费记录
     * @param array $data
     * @return int
     */
    public function addAccount(array $data): int {
        if ($this->db()->insert($data)) {
            return $this->db->lastId();
        }
        return 0;
    }

    /**
     * 更新记录
     * @param int $id
     * @param array $data
     * @return int
     */
    public function updateAccount(int $id, array $data): int {
        return $this->db()->where('account_id', $id)->update($data);
    }

    /**
     * 在线数量
     * @param int $uid
     * @return int
     */
    public function onlineNumber(int $uid = 0): int {
        $this->db()->field('count(*)')->where('end_time', 0)->where('client_ip', '<>', '');
        if (!empty($uid)) {
            $this->db->where('uid', $uid);
        }
        $row = $this->db->find();
        return $row['count(*)'];
    }

    /**
     * 验证session
     * @param $uid
     * @param $session
     * @param $server_id
     * @return bool
     */
    public function verifySession($uid, $session): int {
        $row = $this->db()->where('uid', $uid)->where('session', $session)->where('server_id', $server_id);
        if ($row) {
            if ($row['end_time'] != 0) {
                return 0;
            }
            return $row['account_id'];
        }
        return 0;
    }

}
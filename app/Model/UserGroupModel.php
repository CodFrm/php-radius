<?php
/**
 *============================
 * author:Farmer
 * time:2018/7/14 14:31
 * blog:blog.icodef.com
 * function:用户所属用户组模型
 *============================
 */


namespace App\Model;


use HuanL\Core\App\Model\DbModel;

class UserGroupModel extends DbModel {
    public const table = 'user_group';

    public const primaryKey = '';

    /**
     * 获取用户的用户组
     * @param int $uid
     * @return array
     */
    public function getUserGroup(int $uid): array {
        $groups = [];
        $record = $this->db('a')->where(['uid' => $uid])->where([
            ['expire', '>', time()],
            ['expire', '=', '-1', 'or']
        ])->select();
        while ($row = $record->fetch()) {
            $groups[] = $row;
        }
        return $groups;
    }


}
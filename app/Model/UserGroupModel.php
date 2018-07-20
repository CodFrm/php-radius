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


use App\Model\Admin\AddUserGroupVerifyModel;
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

    /**
     * 给用户添加一个用户组
     * @param int $uid
     * @param int $gid
     * @return int
     */
    public function addUserGroup(AddUserGroupVerifyModel $addUserGroupVerifyModel): int {
        return $this->db()->insert([
            'uid' => $addUserGroupVerifyModel->uid,
            'group_id' => $addUserGroupVerifyModel->gid,
            'time' => time(),
            'expire' => $addUserGroupVerifyModel->expire
        ]);
    }

    /**
     * 更新用户的用户组
     * @param AddUserGroupVerifyModel $addUserGroupVerifyModel
     * @return int
     */
    public function updateUserGroup(AddUserGroupVerifyModel $addUserGroupVerifyModel): int {
        return $this->db()->where([
            'uid' => $addUserGroupVerifyModel->uid,
            'group_id' => $addUserGroupVerifyModel->before
        ])->update([
            'group_id'=>$addUserGroupVerifyModel->gid,
            'expire' => $addUserGroupVerifyModel->expire,
            'time' => time()
        ]);
    }

    /**
     * 删除用户的用户组
     * @param AddUserGroupVerifyModel $addUserGroupVerifyModel
     * @return int
     */
    public function deleteUserGroup(AddUserGroupVerifyModel $addUserGroupVerifyModel): int {
        return $this->db()->where([
            'uid' => $addUserGroupVerifyModel->uid,
            'group_id' => $addUserGroupVerifyModel->gid
        ])->delete();
    }
}
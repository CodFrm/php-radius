<?php
/**
 *============================
 * author:Farmer
 * time:2018/7/14 14:30
 * blog:blog.icodef.com
 * function:用户组模型
 *============================
 */


namespace App\Model;


use HuanL\Core\App\Model\DbModel;

class GroupModel extends DbModel {
    public const table = 'group';

    public const primaryKey = 'group_id';

    /**
     * 获取用户组权限
     * @param int $group_id
     * @return array
     */
    public function getGroupAuth(int $group_id): array {
        return self::exist($group_id);
    }
}


<?php
/**
 *============================
 * author:Farmer
 * time:2018/7/19 21:56
 * blog:blog.icodef.com
 * function:
 *============================
 */


namespace App\Model\Admin;


use App\Model\GroupModel;
use App\Model\UserGroupModel;
use App\Model\UserModel;
use HuanL\Core\App\Model\VerifyModel;
use HuanL\Verify\Rule;

class AddUserGroupVerifyModel extends VerifyModel {

    /**
     * @verify empty 用户uid不能为空
     * @verify regex /^[0-9]{1,10}$/ uid只能为数字
     * @verify func uid
     * @var int
     */
    public $uid;

    /**
     * @verify empty 用户组id不能为空
     * @verify regex /^[0-9]{1,10}$/ gid只能为数字
     * @verify func gid
     * @var int
     */
    public $gid;

    /**
     * @verify empty 到期时间不能为空
     * @verify regex /^-?[0-9]{1,13}$/ 到期时间只能为数字
     * @var int
     */
    public $expire;

    /**
     * 之前的用户组id
     * @verify empty true 可以为空
     * @verify regex /^[0-9]{1,10}$/ 之前的用户组id只能为数字
     * @verify func before
     * @var int
     */
    public $before;

    private $type = '';

    /**
     * 返回将操作的类型
     * @return string
     */
    public function type(): string {
        return $this->type;
    }

    public function uid($uid) {
        if (UserModel::exist($uid)) {
            return true;
        }
        return '不存在的用户';
    }

    public function gid($gid) {
        if (GroupModel::exist($gid)) {
            return true;
        }
        return '不存在的用户组';
    }

    public function before($gid) {
        //如果之前的用户组存在,那就是更新
        $newGroup = UserGroupModel::exist([
            'uid' => $this->uid,
            'group_id' => $this->gid
        ]);
        if (UserGroupModel::exist([
            'uid' => $this->uid,
            'group_id' => $gid
        ])) {
            $this->type = 'update';
            //更新需要判断准备更新的用户组有没有相同的,并且老id和新id不相等
            if ($newGroup && $this->gid != $this->before) {
                //如果有相同的用户组,返回错误
                return '已经添加了一个相同的用户组';
            }
            return true;
        }
        //在判断新增的用户组存不存在,存在就是更新,不存在就是新增
        if ($newGroup) {
            $this->before = $this->gid;
            $this->type = 'update';
        } else {
            $this->type = 'insert';
        }
        return true;
    }

}
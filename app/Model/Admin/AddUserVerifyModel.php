<?php
/**
 *============================
 * author:Farmer
 * time:2018/7/20 16:19
 * blog:blog.icodef.com
 * function:
 *============================
 */


namespace App\Model\Admin;


use App\Model\GroupModel;
use App\Model\UserModel;
use HuanL\Core\App\Model\VerifyModel;

class AddUserVerifyModel extends VerifyModel {

    /**
     * @verify empty 用户名不能为空
     * @verify length 2,10 用户名不符合规则(2-10个字符)
     * @verify regex /^(?=.*[a-zA-Z])[\w]+$/ 用户名格式不正确,只能使用数字字母组合,并且必须包含一个字母
     * @verify func user
     * @var string
     */
    public $user;

    /**
     * @verify empty 密码不能为空
     * @verify length 6,16 密码不符合规则(6-16个字符)
     * @verify regex /^[\x20-\x7e]{6,16}$/ 有错误的字符
     * @var string
     */
    public $passwd;

    /**
     * @verify empty 邮箱不能为空
     * @verify length 3,32 邮箱长度错误(3-16个字符)
     * @verify regex /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/ 邮箱格式错误
     * @verify func email
     * @var string
     */
    public $email;

    /**
     * @verify empty 用户组不能为空
     * @verify func group
     * @var array
     */
    public $group;


    public function group($group) {
        if (is_array($group)) {
            $tmp = [];
            foreach ($group as $item) {
                if (isset($item['expire']) && isset($item['group_id'])) {
                    if (!GroupModel::exist($item['group_id'])) {
                        return '有不存在的用户组';
                    }
                } else {
                    return '用户组格式错误';
                }
                $tmp['gid_' . $item['group_id']] = (isset($tmp['gid_' . $item['group_id']]) ? $tmp['gid_' . $item['group_id']] : 0) + 1;
                if ($tmp['gid_' . $item['group_id']] > 1) return '错误的用户组格式';
            }
            return true;
        }
        return '用户组需要是一个数组';
    }

    public function user($user) {
        if (UserModel::exist(['user' => $user])) {
            //有重名
            return '注册失败,该用户名已经被注册';
        }
        //无重名
        return true;
    }

    public function email($email) {
        if (UserModel::exist(['email' => $email])) {
            //有重名
            return '注册失败,该邮箱已经被注册';
        }
        //无重名
        return true;
    }
}
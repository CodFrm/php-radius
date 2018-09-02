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


use App\Model\UserModel;
use HuanL\Core\App\Model\VerifyModel;

class UpdateUserVerifyModel extends VerifyModel {

    /**
     * @verify empty 用户uid不能为空
     * @verify regex /^[0-9]{1,10}$/ uid只能为数字
     * @verify func uid
     * @var int
     */
    public $uid;

    /**
     * @verify empty true 可以为空
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
     * @var string
     */
    public $user;

    public function email($email) {
        if ($user = UserModel::exist(['email' => $email])) {
            //有重名
            if ($user['uid'] == $this->uid) {
                return true;
            }
            return '添加失败,该邮箱已经被注册';
        }
        //无重名
        return true;
    }

    public function uid($uid) {
        if ($this->user = UserModel::exist(['uid' => $uid])) {
            return true;
        }
        return '不存在的用户';
    }
}
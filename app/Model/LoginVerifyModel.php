<?php

namespace App\Model;


use HuanL\Core\App\Model\VerifyModel;

class LoginVerifyModel extends VerifyModel {

    /**
     * @verify empty 用户名不能为空
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

    public function user($user) {
        if (strpos($user, '@') > 1) {
            //邮箱验证
            if (preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $user)) {
                return true;
            }
            return '邮箱格式不正确';
        } else {
            if (preg_match('/^(?=.*[a-zA-Z])[\w]{1,10}$/', $user)) {
                return true;
            }
            return '用户名格式不正确';
        }
    }
}
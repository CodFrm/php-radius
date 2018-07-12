<?php
/**
 * Created by PhpStorm.
 * User: codef
 * Date: 2018/7/12
 * Time: 12:50
 */

namespace App\Model;


use HuanL\Core\App\Model\VerifyModel;

class LoginVerifyModel extends VerifyModel {

    /**
     * @verify empty 用户名不能为空
     * @verify length 2,10 用户名不符合规则(2-10个字符)
     * @verify regex /^[\w]+$/ 用户名格式不正确
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


}
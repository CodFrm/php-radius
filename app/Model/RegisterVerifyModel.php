<?php

namespace App\Model;


use HuanL\Core\App\Model\VerifyModel;
use HuanL\Db\Db;
use HuanL\Request\Request;

class RegisterVerifyModel extends VerifyModel {

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
     * @verify empty 第二次密码不能为空
     * @verify equal :passwd 两次输入的密码不同
     * @var string
     */
    public $confirm;

    /**
     * @verify empty 邮箱不能为空
     * @verify length 3,32 邮箱长度错误(3-16个字符)
     * @verify regex /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/ 邮箱格式错误
     * @verify func email
     * @var string
     */
    public $email;


    public function user($user) {
        if (UserModel::exist(['user' => $user])) {
            //有重名
            return '注册失败,该用户名已经被注册';
        }
        //无重名,校验ip
        /** @var Request $req */
        $req = app(Request::class);
        if ($row = UserModel::getIpLastReg($req->getip())) {
            $model = new ConfigModel();
            if ($row['reg_time'] + $model->getConfigVal('reg_interval') > time()) {
                return '注册时间间隔过短';
            }
        }
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
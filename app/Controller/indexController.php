<?php

namespace App\Controller;

use App\ErrorCode;
use App\Model\LoginVerifyModel;
use App\Model\RegisterVerifyModel;
use App\Model\UserModel;
use HuanL\Core\Facade\Db;

/**
 * Class indexController
 * @package App\Controller
 */
class indexController extends ViewController {

    /**
     * @route get /
     */
    public function index() {
        return $this->view();
    }


    public function login() {
        return $this->view();
    }

    public function register() {
        return $this->view();
    }

    /**
     * @route post /login
     */
    public function postLogin() {
        $loginVerify = new LoginVerifyModel($_POST);
        if ($loginVerify->__check()) {
            return new ErrorCode(0);
        }
        return new ErrorCode(-1, $loginVerify->getLastError());
    }


    /**
     * @route post /register
     */
    public function postRegister() {
        $regVerify = new RegisterVerifyModel($_POST);
        if ($regVerify->__check()) {
            $user = new UserModel();
            Db::begin();
            if ($uid = $user->register($regVerify)) {
                Db::commit();
                return new ErrorCode(0, '注册成功', ['uid' => $uid]);
            }
            Db::rollback();
            return new ErrorCode(-1, '注册失败');
        }
        return new ErrorCode(-1, $regVerify->getLastError());
    }
}
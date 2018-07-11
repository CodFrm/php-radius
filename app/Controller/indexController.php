<?php

namespace App\Controller;

use App\ErrorCode;

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

        return new ErrorCode(0, 'success');
    }


}
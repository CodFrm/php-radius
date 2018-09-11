<?php


namespace App\Controller\Admin;


use App\Controller\ViewController;

class homeController extends adminAuthController {

    /**
     * @route get /admin
     * @return \HuanL\Viewdeal\View
     */
    public function home() {
        return $this->view();
    }

    /**
     * @return \HuanL\Viewdeal\View
     */
    public function user() {
        return $this->view();
    }

    public function server() {
        return $this->view();
    }

}
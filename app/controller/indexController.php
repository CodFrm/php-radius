<?php

namespace App\Controller;

use HuanL\Core\App\Controller\Controller;

/**
 * Class indexController
 * @package App\Controller
 */
class indexController extends Controller {

    /**
     * @route /
     */
    public function index() {
        return $this->view();
    }

}
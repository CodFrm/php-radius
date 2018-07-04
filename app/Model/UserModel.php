<?php


namespace App\Model;


use HuanL\Core\App\Model\DbModel;

class UserModel extends DbModel {

    protected $table = 'users';

    protected $primaryKey = 'uid';

    public function __construct() {
        parent::__construct('users');
    }


}
<?php
/**
 * Created by PhpStorm.
 * User: codef
 * Date: 2018/7/11
 * Time: 10:24
 */

namespace App;


use HuanL\Request\Response;

class ErrorCode extends Response {

    public $code = [
        0 => 'success'
    ];

    public function __construct(int $code, $msg = '', array $attr = []) {
        $send = array_merge(['code' => $code, 'msg' => $msg], $attr);
        parent::__construct(200,'json',$send);
    }

}
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

    public function __construct(int $code, $msg = true, array $attr = []) {
        $send = ['code' => $code];
        if (is_string($msg)) {
            $send['msg'] = $msg;
        } else if ($msg) {
            $send['msg'] = $this->code[$code] ?? null;
        }
        $send = array_merge($send, $attr);
        parent::__construct(200, 'json', $send);
    }

}
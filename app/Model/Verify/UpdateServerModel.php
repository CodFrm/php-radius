<?php
/**
 *============================
 * author:Farmer
 * time:2018/7/25 10:52
 * blog:blog.icodef.com
 * function:验证添加服务器的数据
 *============================
 */


namespace App\Model\Verify;


use App\Model\ServerModel;
use HuanL\Core\App\Model\VerifyModel;

class UpdateServerModel extends VerifyModel {

    /**
     * @verify empty 服务器id不能为空
     * @verify func server_id
     * @var int
     */
    public $server_id;

    /**
     * @verify empty 服务器名不能为空
     * @verify length 2,10 名称不符合规则(2-10个字符)
     * @verify regex /^[\x{4e00}-\x{9fa5}\w]+$/u 用户名格式错误,只允许输入中/英/数字/下划线
     * @var string
     */
    public $name;

    /**
     * @verify empty 服务器ip不能为空
     * @verify regex /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/ ip格式不正确(ipv4)
     * @var string
     */
    public $ip;

    /**
     * @verify empty 配置内容不能为空
     * @verify length 20,4096 配置长度不正确(20-4096个字符)
     * @var string
     */
    public $config;

    /**
     * @verify empty 秘钥不能为空
     * @verify regex /^\w{6,12}$/ 秘钥格式不正确(6-12个中/英/数字/下划线)
     * @var string
     */
    public $secret;

    public function server_id($id) {
        if (ServerModel::exist($id)) {
            return true;
        }
        return '不存在的项目';
    }
}
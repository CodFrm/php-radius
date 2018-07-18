<?php


namespace App\Controller\Admin;

use App\ErrorCode;
use App\Model\GroupModel;
use App\Model\UserGroupModel;
use App\Model\UserModel;
use HuanL\Core\App\Controller\ApiController as RestfulController;
use HuanL\Request\Request;

class apiController extends adminAuthController {

    use RestfulController;

    public function __construct(Request $request) {
        parent::__construct($request);
        $this->apiField = '';
    }

    private function user() {
        return 'error action';
    }

    /**
     * @api
     * @func 获取用户列表
     * @url admin/api/user
     * @method get
     * @param int 页码:page default:1 翻页的页码
     * @demo admin/api/user?page=2 get {"code":0,"msg":"success","rows":[{"uid":"1","user":"farmer","email":"code.farmer@qq.com","reg_time":"1531792947","last_login_time":"1531792947"}],"total":1}
     * @return array
     */
    public function getUser() {
        $page = $_GET['page'] ?? 1;
        $users = new UserModel();
        $users->db();
        $total = 0;
        $rows = $users->pagination($page, ['uid', 'user', 'email', 'reg_time', 'last_login_time'], 20, $total);
        return ['code' => 0, 'msg' => 'success', 'rows' => $rows, 'total' => $total];
    }

    private function userGroup() {
        return 'error action';
    }

    /**
     * @api
     * @func 获取用户组列表
     * @url admin/api/usergroup
     * @method get
     * @param int 用户id:uid default:null 如果为null,则将返回全部的用户组,有用户id则返回用户存在的uid
     * @demo admin/api/usergroup get
     * @return array
     */
    public function getUserGroup() {
        $rows = [];
        if (isset($_GET['uid']) && !empty($_GET['uid'])) {
            //有uid参数
            $model = new UserGroupModel();
            $rows = $model->getUserGroup($_GET['uid']);
        } else {
            //无uid参数
            $model = new GroupModel();
            $rows = $model->getGroupList();
        }
        return new ErrorCode(0, 'success', ['rows' => $rows]);
    }

}

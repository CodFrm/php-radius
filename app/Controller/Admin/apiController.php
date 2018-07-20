<?php


namespace App\Controller\Admin;

use App\ErrorCode;
use App\Model\Admin\AddUserGroupVerifyModel;
use App\Model\Admin\AddUserVerifyModel;
use App\Model\GroupModel;
use App\Model\UserGroupModel;
use App\Model\UserModel;
use HuanL\Core\App\Controller\ApiController as RestfulController;
use HuanL\Core\Facade\Db;
use HuanL\Request\Request;

class apiController extends adminAuthController {

    use RestfulController;

    public function __construct(Request $request) {
        parent::__construct($request);
        $this->apiField = '';
        if (empty($_POST)) {
            $_POST = $this->request->post();
        }
    }

    private function user() {
        return 'error action';
    }

    /**
     * @api get admin/api/user
     * @apiName 用户列表
     * @apiDescription 获取用户基本信息列表
     * @apiParam int 页码:page default:1 翻页的页码
     * @apiReqdemo admin/api/user?page=1 get
     * @apiResdemo json {"code":0,"msg":"success","rows":[{"uid":"1","user":"farmer","email":"code.farmer@qq.com","reg_time":"1531792947","last_login_time":"1531792947"}],"total":1}
     * @apiReturn json
     */
    public function getUser(): array {
        $page = $_GET['page'] ?? 1;
        $users = new UserModel();
        $users->db();
        $total = 0;
        $rows = $users->pagination($page, ['uid', 'user', 'email', 'reg_time', 'last_login_time'], 20, $total);
        return ['code' => 0, 'msg' => 'success', 'rows' => $rows, 'total' => $total];
    }

    /**
     * @api
     * @func 增加新用户
     * @url admin/api/user
     * @method post
     * @return ErrorCode
     */
    public function postUser(): ErrorCode {
        $vmodel = new AddUserVerifyModel($_POST);
        if ($vmodel->__check()) {
            $umodel = new UserModel();
            Db::begin();
            if (!($uid = $umodel->addNewUser($vmodel))) {
                return new ErrorCode(-1, '添加失败');
            }
            $ugmodel = new UserGroupModel();
            foreach ($vmodel->group as $item) {
                $ugmodel->addUserGroup(new AddUserGroupVerifyModel([
                    'gid' => $item['group_id'],
                    'uid' => $uid,
                    'expire' => $item['expire']
                ]));
            }
            Db::commit();
            return new ErrorCode(0);
        }
        return new ErrorCode(-1, $vmodel->getLastError());
    }


    /**
     * @api
     * @func 修改用户信息
     * @url admin/api/user
     * @method put
     * @return array
     */
    public function putUser() {

    }

    private function userGroup() {
        return 'error action';
    }

    /**
     * @api get admin/api/usergroup
     * @apiName 用户组列表
     * @apiDescription 获取用户组列表,如果提交用户uid可以获取该用户的用户组
     * @apiParam int 用户id:uid default:null 如果为null,则将返回全部的用户组,有用户id则返回用户存在的uid
     * @apiReqdemo admin/api/usergroup get
     * @apiResdemo json {"code":0,"msg":"success","rows":[{"group_id":"1","name":"管理员","auth_id":"1,2","description":"管理员,拥有至高的权限"},{"group_id":"2","name":"普通用户","auth_id":"2","description":"底层愚民"}]}
     * @apiReqdemo admin/api/usergroup?uid=1 get
     * @apiResdemo json {"code":0,"msg":"success","rows":[{"uid":"1","group_id":"1","time":"0","expire":"-1"},{"uid":"1","group_id":"2","time":"0","expire":"1535977639"}]}
     * @apiReturn json
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

    /**
     * @api post admin/api/usergroup
     * @apiDescription 给用户添加/更新用户组
     * @apiBody int 用户id:uid 用户uid
     * @apiBody int 用户组id:gid 用户组id
     * @APIBody int 老用户组id:before default:0 之前的用户组id,为空则为新增
     * @apiSuccessDemo
     * @apiReqdemo admin/api/usergroup post {"uid":1,"gid":"2"}
     * @apiResdemo json
     * @apiErrorDemo
     * @apiReqdemo admin/api/usergroup post {"uid":-1,"gid":"2"}
     * @apiResdemo json {"code":-1,"msg":"用户uid不能为空"}
     * @apiReturn json
     */
    public function postUserGroup() {
        $vmodel = new AddUserGroupVerifyModel($_POST);
        if ($vmodel->__check() === true) {
            $umodel = new UserGroupModel();
            if ($vmodel->type() == 'update') {
                //存在更新时间
                $umodel->updateUserGroup($vmodel);
            } else {
                //不存在,插入一条新的
                $umodel->addUserGroup($vmodel);
            }
            return new ErrorCode(0);
        }
        return new ErrorCode(-1, $vmodel->getLastError());
    }

    /**
     * @api delete admin/api/usergroup
     * @apiDescription 删除用户的用户组
     * @apiBody int 用户id:uid 用户uid
     * @apiBody int 用户组:gid 用户组id
     * @apiSuccessDemo
     * @apiReqdemo admin/api/usergroup delete
     * @apiResdemo json
     * @apiErrorDemo
     * @apiReqdemo admin/api/usergroup delete
     * @apiResdemo json
     * @apiReturn json
     */
    public function deleteUserGroup() {
        $vmodel = new AddUserGroupVerifyModel($_POST);
        if ($vmodel->__check() === true) {
            $umodel = new UserGroupModel();
            $umodel->deleteUserGroup($vmodel);
            return new ErrorCode(0);
        }
        return new ErrorCode(-1, $vmodel->getLastError());
    }
}

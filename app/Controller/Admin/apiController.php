<?php


namespace App\Controller\Admin;

use App\ErrorCode;
use App\Model\Admin\AddUserGroupVerifyModel;
use App\Model\Admin\AddUserVerifyModel;
use App\Model\Admin\UpdateUserVerifyModel;
use App\Model\ConfigModel;
use App\Model\GroupModel;
use App\Model\ServerModel;
use App\Model\UserGroupModel;
use App\Model\UserModel;
use App\Model\Verify\AddServerModel;
use App\Model\Verify\UpdateServerModel;
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
        if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
            $users->db->where('uid', $_GET['keyword'])
                ->_or()->where('user', $_GET['keyword'])
                ->_or()->where('email', $_GET['keyword'])
                ->_or()->where('reg_ip', $_GET['keyword']);
        }
        $rows = $users->pagination($page, ['uid', 'status', 'user', 'email', 'reg_time', 'last_login_time'], 20, $total);
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
     * @return ErrorCode
     */
    public function putUser() {
        $vmodel = new UpdateUserVerifyModel($_POST);
        if ($vmodel->__check()) {
            $umodel = new UserModel();
            Db::begin();
            $umodel->updateUser($vmodel);
            Db::commit();
            return new ErrorCode(0);
        }
        return new ErrorCode(-1, $vmodel->getLastError());
    }

    /**
     * @api
     * @func 禁封/解封用户
     * @url admin/api/user
     * @method delete
     * @return ErrorCode
     */
    public function deleteUser() {
        if (isset($_POST['uid']) && isset($_POST['status'])) {
            $umodel = new UserModel();
            $umodel->updateStatus($_POST['uid'], $_POST['status']);
            return new ErrorCode(0);
        }
        return new ErrorCode(-1, '缺少必要参数');
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

    private function server() {
        return 'error action';
    }

    /**
     * 获取服务器信息
     * @param ServerModel $userModel
     * @param int $page
     * @param bool $simple
     * @return array
     */
    public function getServer(ServerModel $userModel, $page = 1, $simple = true) {
        $page = $page ?? 1;
        $userModel->db()->where('status in (0,1)');
        $total = 0;
        if ($simple === true) {
            $fields = ['server_id', 'name', 'ip', 'config', 'secret', 'status'];
        } else {
            $fields = ['name', 'ip', 'secret'];
        }
        $rows = $userModel->pagination($page, $fields, 20, $total);
        return ['code' => 0, 'msg' => 'success', 'rows' => $rows, 'total' => $total];
    }

    /**
     * 添加服务器
     * @param AddServerModel $addServerModel
     * @param ServerModel $serverModel
     * @return ErrorCode
     */
    public function postServer(AddServerModel $addServerModel, ServerModel $serverModel) {
        $addServerModel->setCheckData($_POST);
        if ($addServerModel->__check()) {
            if ($serverModel->addServer($addServerModel)) {
                return new ErrorCode(0);
            }
            return new ErrorCode(-1, '未知错误');
        }
        return new ErrorCode(-1, $addServerModel->getLastError());
    }

    /**
     * 修改服务器
     * @param UpdateServerModel $updateServerModel
     * @param ServerModel $serverModel
     * @return ErrorCode
     */
    public function putServer(UpdateServerModel $updateServerModel, ServerModel $serverModel) {
        $updateServerModel->setCheckData($_POST);
        if ($updateServerModel->__check()) {
            if ($serverModel->updateServer($updateServerModel)) {
                return new ErrorCode(0);
            }
            return new ErrorCode(-1, '未知错误');
        }
        return new ErrorCode(-1, $updateServerModel->getLastError());
    }

    /**
     * 删除服务器
     * @param ServerModel $serverModel
     * @return ErrorCode|string
     */
    public function deleteServer(ServerModel $serverModel) {
        if (!isset($_POST['server_id'])) {
            return 'error action';
        }
        switch ($_GET['type'] ?? 0) {
            case 1:
                {
                    if ($serverModel->updateServerState($_POST['server_id'], $_POST['status'] ?? 0)) {
                        return new ErrorCode(0);
                    }
                    break;
                }
            case 2:
                {
                    if ($serverModel->updateServerState($_POST['server_id'], 2)) {
                        return new ErrorCode(0);
                    }
                    break;
                }
            default:
                {
                    return 'error action';
                }
        }
        return new ErrorCode(-1, '系统错误');
    }

    private function setting() {
        return 'error action';
    }

    /**
     * 获取设置
     * @return ErrorCode
     */
    public function getSetting() {
        $model = new ConfigModel();
        $config = [
            'online_num' => $model->getConfigVal('online_num'),
            'reg_interval' => $model->getConfigVal('reg_interval')
        ];
        return new ErrorCode(0, 'success', ['config' => $config]);
    }

    /**
     * 修改设置
     * @return ErrorCode
     */
    public function putSetting() {
        $model = new ConfigModel();
        $model->setConfigVal('online_num', $_POST['online_num']);
        $model->setConfigVal('reg_interval', $_POST['reg_interval']);
        return new ErrorCode(0, 'success');
    }

    /**
     * 获取系统信息
     * @return ErrorCode
     */
    public function sysmsg(): ErrorCode {
        $usercount = Db::table('user')->count();
        $server = Db::table('server')->where('status', 0)->count();
        return new ErrorCode(0, 'success', ['user' => $usercount, 'server' => $server]);
    }

}

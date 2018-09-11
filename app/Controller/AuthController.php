<?php
/**
 *============================
 * author:Farmer
 * time:2018/7/14 14:56
 * blog:blog.icodef.com
 * function:
 *============================
 */


namespace App\Controller;


use App\Model\GroupModel;
use App\Model\TokenModel;
use App\Model\UserGroupModel;
use App\Model\UserModel;
use HuanL\Request\Request;
use HuanL\Request\Response;

class AuthController extends ViewController {

    /**
     * 登录的用户的uid
     * @var int
     */
    protected $uid;

    /**
     * 现在访问的操作的权限id
     * @var int
     */
    protected $nowAuthId = 0;

    /**
     * 本控制器的权限id
     * @var int
     */
    protected const controllerAuthId = 0;

    /**
     * 操作的权限id列表,action=>id
     * @var array
     */
    protected const authIdList = [];

    public function __construct(Request $request) {
        parent::__construct($request);
        $this->uid = $_COOKIE['uid'] ?? 0;
        if (!$this->isLogin()) {
            /** @var Response $res */
            $res = app(Response::class);
            $res->redirection($this->request->home(true) . '/login');
            die('not login');
        }
        /** @var UserModel $umodel */
        $umodel = new UserModel();
        if ($umodel->getUserState($this->uid) != 0) {
            die('账号被封');
        }
        $this->nowAuthId = static::authIdList[$this->action] ?? static::controllerAuthId;
        /** @var UserGroupModel $userAuthModel */
        $userAuthModel = new UserGroupModel();
        $groups = $userAuthModel->getUserGroup($this->uid);
        $success = static::auth($this->nowAuthId, $groups);
        if (!$success) {
            die('没有权限');
        }
    }

    public static function auth($authId, $groups) {
        /** @var GroupModel $groupModel */
        $groupModel = new GroupModel();
        $flag = false;
        foreach ($groups as $item) {
            $groupAuth = $groupModel->getGroupAuth($item['group_id']);
            $groupAuths = explode(',', $groupAuth['auth_id']);
            if (in_array($authId, $groupAuths)) {
                $flag = true;
                break;
            }
        }
        return $flag;
    }

    /**
     * 判断是否登录
     * @return bool
     */
    public function isLogin(): bool {
        $token = $_COOKIE['token'] ?? '';
        /** @var TokenModel $tokenModel */
        $tokenModel = new TokenModel();
        return $tokenModel->verifyToken($token, $this->uid, TokenModel::LOGIN, 604800, true);
    }


}
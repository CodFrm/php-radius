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
        $this->nowAuthId = static::authIdList[$this->action] ?? static::controllerAuthId;
        /** @var UserGroupModel $userAuthModel */
        $userAuthModel = app(UserGroupModel::class);
        $groups = $userAuthModel->getUserGroup($this->uid);
        /** @var GroupModel $groupModel */
        $groupModel = app(GroupModel::class);
        $flag = false;
        foreach ($groups as $item) {
            $groupAuth = $groupModel->getGroupAuth($item['group_id']);
            $groupAuths = explode(',', $groupAuth['auth_id']);
            if (in_array($this->nowAuthId, $groupAuths)) {
                $flag = true;
                break;
            }
        }
        if (!$flag) {
            die('没有权限');
        }
    }

    /**
     * 判断是否登录
     * @return bool
     * @throws \HuanL\Container\InstantiationException
     */
    public function isLogin(): bool {
        $token = $_COOKIE['token'] ?? '';
        /** @var TokenModel $tokenModel */
        $tokenModel = app(TokenModel::class);
        return $tokenModel->verifyToken($token, $this->uid, TokenModel::LOGIN, 604800, true);
    }


}
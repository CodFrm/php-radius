<?php


namespace App\Controller\Admin;


use App\Controller\ViewController;
use HuanL\Request\Request;
use HuanL\Routing\Route;

class adminAuthController extends ViewController {

    public function __construct(Request $request) {
        parent::__construct($request);

    }

    protected $namespaceUrl = '/admin';

    public $menu = [
        '首页' => ['url' => '', 'icon' => 'home'],
        '用户管理' => ['url' => 'user', 'icon' => 'user'],
        '服务器管理' => ['url' => 'server', 'icon' => 'server'],
        '设置' => ['url' => 'setting', 'icon' => 'shezhi']
    ];

    public function echoMenu($menu, &$active = false) {
        $ret = '';
            foreach ($menu as $key => $item) {
            if ($item == 'br') {
                $ret .= '<div class="nav-item br"></div>';
            } else if (is_array($item['url'])) {
                $tmp = $this->echoMenu($item['url'], $isActive);
                $ret .= '  <div class="nav-item';
                if ($isActive) {
                    $active = true;
                    $ret .= ' active';
                }
                $ret .= '"><div class="nav-ic"><i class="iconfont icon-' . $item['icon'] . '"></i> ' . $key .
                    ' <i class="iconfont icon-down"></i></div>';
                $ret .= '<div class="sub-nav">';
                $ret .= $tmp;
                $ret .= '</div></div>';
            } else {
                $ret .= '<a href="' . $this->_url($item['url']) .
                    '" class="nav-item"><div class="nav-ic';
                if ($this->action == $item['url']) {
                    $active = true;
                    $ret .= ' active';
                }
                $ret .= '"><i class="iconfont icon-' . $item['icon'] . '"></i> ' . $key . '</div></a>';
            }
        }
        return $ret;
    }

}
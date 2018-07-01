<?php


namespace App\Controller;


use HuanL\Core\App\Controller\Controller;
use HuanL\Core\Facade\Route;
use HuanL\Request\Request;

class ViewController extends Controller {

    protected $namespaceUrl = '';
    /**
     * @var Request
     */
    protected $request;

    /**
     * 操作
     * @var string
     */
    protected $action = '';

    public function __construct(Request $request) {
        parent::__construct();
        $this->request = $request;
        /** @var \HuanL\Routing\Route $route */
        $route = app(\HuanL\Routing\Route::class);
        $this->action = $route->getParam()['action'] ?? '';
    }

    public function _js($file) {
        return '<script type="text/javascript" src="' . $this->request->home() . '/js/' . $file . '"></script>';
    }

    public function _url($action) {
        if ($action) {
            return $this->request->home() . (Route::name($action) ?: $this->namespaceUrl . '/' . $action);
        }
        return $this->request->home() . $this->namespaceUrl;
    }
}
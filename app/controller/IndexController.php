<?php
/**
 * 默认控制器
 */
namespace app\Controller;
use core\lib\Controller;

class IndexController extends Controller{

    /**
     * 默认视图
     */
    public function index()
    {
        $data['title'] = 'ColorPHP';
        $data['userlist'] = $this->userModel->getUserList();
		$this->show('index', $data);
    }
}
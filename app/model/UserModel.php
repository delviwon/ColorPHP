<?php
/**
 * 用户模型
 * User: Jowan
 * Date: 2016/8/31
 * Time: 8:50
 */
namespace app\model;
use core\lib\Model;

class UserModel extends Model{

    public function getUserList()
    {
        $result = $this->select('user');
        return $result;
    }
}
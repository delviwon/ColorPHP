<?php
/**
 * 控制器类
 * @package     ColorPHP
 * @copyright   Copyright (c) 2016 Jowan
 * @author      Jowan<jowan@17code.net>
 * @link        http://www.17code.net
 * @since       Version 1.0.1
 */
namespace core\lib;

class Controller{

    private static $db;

    /**
     * 方法重载
     * @param string $name
     * @param array $args
     */
    public function __call($name, $args)
    {
        $uri = get_uri_info();
        $controller = ucfirst($uri['uri_arr'][0]);
        DEBUG && sys_error("Undefined method <em>{$name}</em> in <em>{$controller}Controller</em>.");
        error("500 Err.");
    }

    /**
     * 属性重载
     * @param $name
     * @return Model
     */
    public function __get($name)
    {
        if (strpos($name, 'Model') !== FALSE)
        {
            $model = "\\app\\model\\" . ucfirst($name);
            return new $model();
        }

        switch ($name)
        {
            case 'db':
                self::$db || self::$db = new Model();
                return self::$db;
                break;

            default:
                DEBUG && sys_error("Undefined variable <em>{$name}</em>.");
                error('500 Err.');
        }
    }

    /**
     * 加载视图
     * @param string $view
     * @param array $data
     */
    public function show($view, $data = array())
    {
        $tpl_dir = get_config('tpl_dir');
        $tpl_dir = $tpl_dir ? $tpl_dir : 'tpl';
        $file = __COLOR__ . '/' . $tpl_dir . '/' . $view . '.' . get_config('tpl_suffix');

        load_file($file , $data);
    }
}
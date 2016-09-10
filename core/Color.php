<?php
/**
 * 系统入口文件
 * @package     ColorPHP
 * @copyright   Copyright (c) 2016 Jowan
 * @author      Jowan<jowan@17code.net>
 * @link        http://www.17code.net
 * @since       Version 1.0.1
 */
namespace core;

class Color{
    /**
     * 加载类库
     * @param string $class
     */
    public static function load($class)
    {
        $class = str_replace('\\', '/', $class);
        $class_file = __COLOR__ . '/' . $class . '.php';


        if (!is_file($class_file))
        {
            DEBUG && sys_error("Class <em>{$class}</em> does not exist!");
            error("500 Err.");
        }

        include_once $class_file;
    }

    /**
     * 框架初始化
     */
    public static function init()
    {
        // 实例化路由
        $route = new lib\Route();

        // 当前控制器
        $controller = $route->controller;

        // 当前方法
        $method = lcfirst($route->method);

        // 加载核心配置
        lib\Config::load('common');

        // 自动加载相关文件
        self::autoload();

        // 控制器路径
        $controller_file = __APP__ . '/controller/' . $controller . 'Controller.php';

        if (!is_file(str_replace('\\', '/', $controller_file)))
        {
           DEBUG && sys_error("Controller <em>{$controller}</em> does not exist!");
           error("500 Err.");
        }
        else
        {
           $controller_path = '\\' . MODULE . "\\controller\\{$controller}Controller";

           try
           {
               $controller = new $controller_path();
               $controller->$method();
           }
           catch (\Exception $e)
           {
               DEBUG && sys_error($e->getMessage());
               error("500 Err.");
           }
        }
    }

    /**
     * 自动加载相关文件
     */
    public static function autoload()
    {
        $autoload_file = __APP__ . '/config/autoload.inc.php';

        if (!is_file($autoload_file))
        {
            return FALSE;
        }

        include_once $autoload_file;

        // 加载配置文件
        foreach ($config as $v)
        {
            lib\config::load($v);
        }

        // 加载函数文件
        foreach ($function as $v)
        {
            $function_file = __COLOR__ . '/' . MODULE ."/common/{$v}.func.php";
            load_file($function_file);
        }
    }
}
<?php
/**
 * 配置类
 * @package     ColorPHP
 * @copyright   Copyright (c) 2016 Jowan
 * @author      Jowan<jowan@17code.net>
 * @link        http://www.17code.net
 * @since       Version 1.0.1
 */
namespace core\lib;

class Config{
    
    static public $config = array();

    /**
     * 加载配置
     * @param string $file
     */
    static public function load($file)
    {
       $sys_config_file = __CORE__ . '/config/' . "{$file}.inc.php";
       $app_config_file = __APP__ . '/config/' . "{$file}.inc.php";

       if (is_file($sys_config_file))
       {
            self::updateConfig($sys_config_file);
       }

       if (is_file($app_config_file))
       {
           self::updateConfig($app_config_file);
       }

       if (!is_file($sys_config_file) && !is_file($app_config_file))
       {
           DEBUG && sys_error(str_replace('\\', '/', __COLOR__) . '/' . MODULE . "/config/{$file}.inc.php does not exist.");
           error("500 Err.");
       }
    }

    /**
     * 更新配置参数
     * @param string $file
     */
    static public function updateConfig($file)
    {
       include_once $file;
       self::$config = array_merge(self::$config, $config);
    }
}
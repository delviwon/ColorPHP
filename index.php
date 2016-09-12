<?php
/**
 * 应用入口文件
 * @package     ColorPHP
 * @copyright   Copyright (c) 2016 Jowan
 * @author      Jowan<jowan@17code.net>
 * @link        http://www.17code.net
 * @since       Version 1.0.1
 */
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('PRC');

/*
 *---------------------------------------------------------------
 * PHP版本检测
 *---------------------------------------------------------------
 */
if (version_compare(PHP_VERSION, '5.3', '<') )
{
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    exit('PHP required >= 5.3!');
}

/*
 *---------------------------------------------------------------
 * 入口文件所在目录
 *---------------------------------------------------------------
 */
$core_file = $_SERVER['DOCUMENT_ROOT'] . '/core/Color.php';
$web = is_file($core_file) ? '' : basename(dirname(__FILE__));

/*
 *---------------------------------------------------------------
 * 定义常用路径
 *---------------------------------------------------------------
 */
define('__WEB__', $web);
define('__PUBLIC__', __WEB__ . '/public/');
define('__COLOR__', realpath('./'));
define('__CORE__', __COLOR__ . '/core');
define('__APP__', __COLOR__ . '/app');
define('MODULE', 'app');

/*
 *---------------------------------------------------------------
 * 定义调试模式
 *---------------------------------------------------------------
 */
define('DEBUG', TRUE);

if (!DEBUG)
{
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
}
else 
{
    error_reporting(-1);
    ini_set('display_errors', 1);
}

/*
 *---------------------------------------------------------------
 * 引入核心文件
 *---------------------------------------------------------------
 */
include __CORE__ . '/common/function/common.func.php';
include __CORE__ . '/Color.php'; 

/*
 *---------------------------------------------------------------
 * 初始化框架
 *---------------------------------------------------------------
 */
spl_autoload_register('\core\Color::load');
\core\Color::init();
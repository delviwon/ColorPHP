<?php
/**
 * 系统函数
 * @package     ColorPHP
 * @copyright   Copyright (c) 2016 Jowan
 * @author      Jowan<jowan@17code.net>
 * @link        http://www.17code.net
 * @since       Version 1.0.1
 */
 
/**
 * 调试函数
 * @param mixed $data
 */
function p($data)
{
    echo '<pre>';
    print_r($data);
}

/**
 * AJAX返回
 * @param $data
 */
function ajax_return($data)
{
    echo json_encode($data);
    exit();
}

/**
 * 成功返回
 * @param string $msg
 * @param string $url
 */
function success($msg, $url = '')
{
    $result = array(
        'status' => 'success',
        'msg' => $msg
    );

    $url && $result['url'] = $url;
    ajax_return($result);
}

/**
 * 错误返回
 * @param string $msg
 * @param string $url
 */
function error($msg, $url = '')
{
    $result = array(
        'status' => 'error',
        'msg' => $msg
    );

    $url && $result['url'] = $url;
    ajax_return($result);
}

/**
 * 系统错误
 * @param string $err_msg
 * @param string $title
 */
function sys_error($err_msg, $title = '错误提示')
{
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    include 'core/tpl/sys_error.php';
    exit();
}

/**
 * 获取URI信息
 * @return array $result
 */
function get_uri_info()
{
    $uri = $_SERVER['REQUEST_URI'];
	
	// 检测URI合法性
    if (!preg_match('/^[a-zA-Z0-9-_\/?=%$.&@-]*$/', $uri))
    {
        DEBUG ? sys_error('Illegal URI!') :  error("500 Err.");
    }
	
	// 过滤真实路径
	if (__WEB__)
	{
		$uri = substr($uri, strpos($uri, __WEB__) + strlen(__WEB__));	
	}

	// 判断URI是否为GET参数
	if (strpos($uri, '?') === 0)
	{
		$uri = '';
	}
	else
	{
		$uri = str_replace('index.php/', '', $uri);
		$uri = strtok(strtok($uri, '?'), '.');
	}

    $uri_arr = array();

    if ($uri != '/')
    {
        $uri_arr = explode('/', trim($uri, '/'));
    }
	
	$path = __APP__ . '/controller/';
    $relative_path = '';

	// 判断第一个参数是否为真实路径
	foreach ($uri_arr as $k => $v)
	{
        $compat_path =  $path . ucfirst($v) . '/';
        $path .=  $v . '/';

        // 忽略大小写
        if (is_dir($compat_path) && $compat_path != $path)
        {
            $url = str_replace($v, ucfirst($v), $_SERVER["REQUEST_URI"]);
            redirect($url);
        }

		if (is_dir($path))
		{
			$relative_path .=  $v . '\\';
			unset($uri_arr[$k]);
			break;
		}
	}

	$result['uri_arr'] = array_values($uri_arr);
	$result['relative_path'] = $relative_path;

    return $result;
}

/**
 * 获取指定uri分段值
 * @param int $n
 * @return string $str
 */
function get_segment($n)
{
    $uri_info = get_uri_info();
	$uri_arr = $uri_info['uri_arr'];
	
    for ($i = 2; $i < count($uri_arr); $i++)
    {
        $segment[] = $uri_arr[$i];
    }

    $str = isset($segment[$n - 1]) ? $segment[$n - 1] : '';
    return $str;
}

/**
 * 获取配置信息
 * @param string $name
 * @return mixed $config
 */
function get_config($name = '')
{
    $config = \core\lib\Config::$config;
    
    if ($name)
    {
        $config = isset($config[$name]) ? $config[$name] : '';
    }

    return $config;
}

/**
 * 生成系统URL
 * @param $uri
 * @return string
 */
function get_url($uri)
{
    $url = __WEB__ . '/' . $uri . '.' . get_config('url_suffix');
    return $url;
}

/**
 * 引入文件
 * @param string $file
 */
function load_file($file, $data = array())
{
    if(!is_file($file))
    {
        DEBUG && sys_error(str_replace('\\', '/', $file) . ' does not exist.');
        error("500 Err.");
    }

    $data && extract($data);
    include_once $file;
}


/**
 * 过滤特殊字符
 * @param mixed $data
 * @param string $mode
 * @return mixed $data
 */
function filter_str($data, $mode = '')
{
    if (PHP_VERSION >= 6 || !get_magic_quotes_gpc())
    {
        $data = is_array($data) ? array_map('addslashes',  $data) : addslashes($data);
    }

    if ($mode == 'html')
    {
        $data = is_array($data) ? array_map('htmlspecialchars', $data) : htmlspecialchars($data);
    }

    return $data;
}

/**
 * 获取GET值
 * @param string $param
 * @param string $mode
 * @return mixed $result
 */
function get($param = '', $mode = '')
{
    if ($param)
    {
        $result = isset($_GET[$param]) ? filter_str($_GET[$param], $mode) : '';
    }
    else
    {
        $result = filter_str($_GET, $mode);
    }

    return $result;
}

/**
 * 获取POST值
 * @param string $param
 * @param string $mode
 * @return mixed $result
 */
function get_post($param = '', $mode = '')
{
    if ($param)
    {
        $result = isset($_POST[$param]) ? filter_str($_POST[$param], $mode) : '';
    }
    else
    {
        $result = filter_str($_POST, $mode);
    }

    return $result;
}

/**
 * 获取REQUEST值
 * @param string $param
 * @param string $mode
 * @return mixed $result
 */
function get_request($param = '', $mode = '')
{
    if ($param)
    {
        $result = isset($_REQUEST[$param]) ? filter_str($_REQUEST[$param], $mode) : '';
    }
    else
    {
        $result = filter_str($_REQUEST, $mode);
    }

    return $result;
}

/**
 * 解析字符串
 * @param string $str
 * @return string $result
 */
function str_decode($str)
{
    $result = htmlspecialchars_decode(stripslashes($str));
    return $result;
}


/**
 * 设置、获取SESSION值
 * @param string $name
 * @param mixed $value
 * @return mixed
 */
function session($name = '', $value = '')
{
    isset($_SESSION) || session_start();

    if ($name && $value)
    {
        $_SESSION[$name] = $value;
    }
    else if ($name && $value === NULL)
    {
        unset($_SESSION[$name]);
    }
    else if ($name && $value === '')
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : '';

    }
    else if (!$name && !$value)
    {
        return $_SESSION;
    }
    else
    {
        DEBUG ? sys_error("Session name can not be NULL.") : error('500 Err.');
    }
}

/**
 * URL跳转
 * @param string $url
 */
function redirect($url)
{
    header("Location:{$url}");
    exit();
}
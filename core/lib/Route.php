<?php
/**
 * 路由处理类
 * @package     ColorPHP
 * @copyright   Copyright (c) 2016 Jowan
 * @author      Jowan<jowan@17code.net>
 * @link        http://www.17code.net
 * @since       Version 1.0.1
 */
namespace core\lib;

class Route{
    
    public $controller;
    public $method;
    
    /**
     * 构造方法
     */
    public function __construct()
    {
		$uri_info = get_uri_info();

		if (isset($uri_info['uri_arr'][0]))
		{
		   $this->controller = $uri_info['relative_path'] . ucfirst($uri_info['uri_arr'][0]);
		}
		
		if (!$this->controller)
		{
			$this->controller = $uri_info['relative_path'] . 'Index';
		}
		
		$this->method = isset($uri_info['uri_arr'][1]) ? strtok($uri_info['uri_arr'][1], '?') : 'index';
    }
}
<?php
/**
 * 文件上传类
 * @package     ColorPHP
 * @copyright   Copyright (c) 2016 Jowan
 * @author      Jowan<jowan@17code.net>
 * @link        http://www.17code.net
 * @since       Version 1.0.1
 */
namespace core\lib;

class Upload{

    public $config;
    public static $errmsg;

    /**
     * 构造方法
     * @param $config
     */
    public function __construct($config = array())
    {
        // 配置参数
        $allowed_type = array('gif', 'jpg', 'jpeg', 'png');
        $this->config['name'] = isset($config['name']) ? $config['name'] : 'image';
        $this->config['limit_size'] = isset($config['limit_size']) ? $config['limit_size'] : 2;
        $this->config['allowed_type'] = isset($config['allowed_type']) ? $config['allowed_type'] : $allowed_type;
        $this->config['save_path'] = isset($config['save_path']) ? $config['save_path'] : 'upload/';
    }

    /**
     * 上传文件
     */
    public function commit()
    {
        $config = $this->config;
        $name = $config['name'];

        // 是否选择文件
        if (!isset($_FILES) || !$_FILES)
        {
            self::$errmsg = '请选择要上传的文件';
            return FALSE;
        }

        // 文件类型限制
        $file_name = explode(".", $_FILES[$name]["name"]);
        $extension = end($file_name);

        if (!in_array($extension, $config['allowed_type']))
        {
            self::$errmsg = '不支持上传此类型的文件';
            return FALSE;
        }

        switch ($_FILES[$name]["error"])
        {
            case 1:
                self::$errmsg = 'UPLOAD_ERR_INI_SIZE';
                break;
            case 2:
                self::$errmsg = 'UPLOAD_ERR_FORM_SIZE';
                break;
            case 3:
                self::$errmsg = 'UPLOAD_ERR_PARTIAL';
                break;
            case 4:
                self::$errmsg = 'UPLOAD_ERR_NO_FILE';
                break;
            case 6:
                self::$errmsg = 'UPLOAD_ERR_NO_TMP_DIR';
                break;
            case 7:
                self::$errmsg = 'UPLOAD_ERR_CANT_WRITE';
                break;
        }

        if ($_FILES[$name]["error"] != 0)
        {
            return FALSE;
        }

        // 文件大小限制
        $file_size = sprintf('%.2f', $_FILES[$name]['size'] / 1024 / 1024);

        if ($file_size > $config['limit_size'] * 1024)
        {
            self::$errmsg = "当前文件大小{$file_size}M超过上传限制{$config['limit_size']}M";
            return FALSE;
        }

        // 上传文件
        $new_file_name = md5(time() . mt_rand(1000, 9999));

        if (!move_uploaded_file($_FILES[$name]["tmp_name"], "{$config['save_path']}{$new_file_name}.{$extension}"))
        {
            self::$errmsg = '上传路径出错';
            return FALSE;
        }

        return TRUE;
    }
}
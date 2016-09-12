<?php
/**
 * 图片验证码类
 * @package     ColorPHP
 * @copyright   Copyright (c) 2016 Jowan
 * @author      Jowan<jowan@17code.net>
 * @link        http://www.17code.net
 * @since       Version 1.0.1
 */
namespace core\lib;

class Captcha{
	private $img;
	private $width;
	private $height;
	private $length;
	private $font;
	private $font_size;
	private $code = '';
	
	/**
	 * 构造方法 验证码初始化 
	 * @param array $config 配置参数
	 * @var int $config['width'] 验证码宽度
	 * @var int $config['height'] 验证码高度
	 * @var int $config['length'] 字符长度
	 * @var string $config['font'] 字体文件路径
	 * @var int $config['font_size'] 字体大小
	 */
	public function __construct($config = array())
	{
		if (!$config) 
		{
			$config = array(
				'width' => 100,
				'height' => 34,
				'length' => 4,
				'font' => __CORE__ . '/common/font/texb.ttf',
				'font_size' => 14
			);
		}

		$this->width = $config['width'];
		$this->height = $config['height'];
		$this->length = $config['length'];
		$this->font = $config['font'];
		$this->font_size = $config['font_size'];
		$this->makeCanvas();
		$this->makeCode();
		$this->makeObstruction();
	}
	
	/**
	 * 新建画布，填充背景
	 */	
	private function makeCanvas()
	{
		$this->img = imagecreatetruecolor($this->width, $this->height);
		$bg_color = imagecolorallocate($this->img, 237, 238, 241);
		imagefill($this->img, 0, 0, $bg_color);
	}
	
	/**
	 * 生成验证码
	 * @var string $seed 种子基因 
	 * @var string $code 验证码 
	 * @var int $x 写入画布x坐标
	 * @var int $y 写入画布y坐标
	 * @var string $angle 旋转角度 
	 */
	private function makeCode()
	{
		$seed = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

		for ($i = 0; $i<$this->length; $i++ )
		{
			$tmp_code = $seed[mt_rand(0,strlen($seed)-1)];	
			$this->code .= $tmp_code;
			$code_color = imagecolorallocate($this->img, rand(0,0), rand(0,0), rand(0,0));		
			$angle = mt_rand(-20,20);
			$x = (($this->width/$this->length) - $this->font_size) / 2 + ($this->width/$this->length) * $i;
			$y = ($this->height + $this->font_size)/2;	
			imagefttext ($this->img, $this->font_size, $angle, $x, $y, $code_color, $this->font, $tmp_code, array());
		}
	}
	
	/**
	 * 设置干扰符
	 */
	private function makeObstruction()
	{
		for($i=0; $i<2; ++$i)
		{
            $x1 = rand(0, $this->width);
            $y1 = rand($this->height / 3, $this->height * (2 / 3));
            $x2 = rand(0, $this->width);
			$line_color = imagecolorallocate($this->img, rand(0, 0), rand(0, 0), rand(0, 0));
            imageline ($this->img, $x1, $y1, $x2, $y1, $line_color);
        } 
	}
	
	/**
	 * 输出验证码
	 */
	public function getCode($code_name = 'code')
    {
        $code = strtolower($this->code);
        session($code_name, $code);
        header("Content-type:image/png");
        imagepng($this->img);
        imagedestroy($this->img);
    }
}
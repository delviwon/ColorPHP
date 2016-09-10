<?php
namespace app\controller\Console;
use core\lib\Controller;

class IndexController extends Controller{
   
    public function index()
    {
		echo get_url('home/index');
    }
}
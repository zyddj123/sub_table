<?php
//非法访问
if (!defined('BASECHECK')){
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
	exit;
}

/**
 * 应用系统入口
 * 
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 *
 */
class IndexController extends Controller{
	
	/**
	 * 默认加载函数
	 * 控制器在初始化时会运行此函数
	 */
	protected function _init(){
		
	}
	
	/**
	 * 默认入口
	 */
	function run(){
		$this->index();	
	}
	
	/**
	 * 自定义功能函数
	 */
	function index(){
		$this->render('index');
	}
}
?>
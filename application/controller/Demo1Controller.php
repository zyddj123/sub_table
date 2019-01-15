<?php
//非法访问
if (!defined('BASECHECK')){
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
	exit;
}

/**
 * 演示Demo Lesson 1，包括：
 * 控制器命名方式
 * 控制器默认加载函数
 * 控制器默认入口函数
 * 自定义控制器函数
 * 
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 *
 */
class Demo1Controller extends Controller{
	private $db='';
	/**
	 * 默认加载函数
	 * 控制器在初始化时会运行此函数
	 */
	protected function _init(){
		$this->db = GetDB();
	}
	
	/**
	 * 默认入口
	 */
	function run(){
		
	}

	function insertData(){

	}

	function readData(){

	}
	
}
?>
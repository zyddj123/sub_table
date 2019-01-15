<?php
//非法访问
if (!defined('BASECHECK')){
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
	exit;
}

/**
 * 演示Demo Lesson 3，数据库操作：
 * 数据库配置
 * 数据库基本操作
 * 
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 *
 */
class Demo3Controller extends Controller{
	
	/**
	 * 默认加载函数
	 * 控制器在初始化时会运行此函数
	 */
	protected function _init(){
		return true;
	}
	
	/**
	 * 默认入口
	 */
	function run(){
		$this->demo3_1();
	}
	
	/**
	 * 数据库配置
	 */
	function demo3_1(){
		$this->Render('demo3/demo3_1');
	}
	
	/**
	 * 数据库基本操作
	 */
	function demo3_2(){
		$this->Render('demo3/demo3_2');
	}
}
?>
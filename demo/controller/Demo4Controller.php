<?php
//非法访问
if (!defined('BASECHECK')){
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
	exit;
}

/**
 * 演示Demo Lesson 4，语言包：
 * 
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 *
 */
class Demo4Controller extends Controller{
	
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
		$this->demo4_1();
	}
	
	/**
	 * 默认语言
	 */
	function demo4_1(){
		//加载语言包
		$this->GetLang('standard');
		$this->Render('demo4/demo4_1');
	}
	
	/**
	 * 手动设置语言
	 */
	function demo4_2(){
		//手动设置语言
		$this->config->Set("language", "en");
		//加载语言包
		$this->GetLang('standard');
		$this->Render('demo4/demo4_2');
	}
}
?>
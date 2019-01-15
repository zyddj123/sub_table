<?php
//非法访问
if (!defined('BASECHECK')){
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
	exit;
}

/**
 * 演示Demo Lesson 2，包括：
 * 引入视图
 * APP_HTTP_ROOT及APP_URL_ROOT常量使用方法
 * 视图变量使用方法
 * 引入多个视图
 * 视图多主题特性
 * 
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 *
 */
class Demo2Controller extends Controller{
	
	private $__val;
	
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
		$this->demo2_1();
	}
	
	/**
	 * 引用视图的方法
	 * 常量的使用
	 */
	function demo2_1(){
		$this->Render('demo2/demo2_1');
	}
	
	/**
	 * 视图传递参数
	 */
	function demo2_2(){
		$this->__val = 'hello';
		
		$this->Render(
				'demo2/demo2_2',
				array(
						'value_1'=>$this->__val,
						'value_2'=>'world'
				)
		);
	}
	
	/**
	 * 设置主题
	 */
	function demo2_3(){
		if(rand(0, 1)==1){
			$this->SetThemes('red');
		}
		$this->Render(
				'demo2/demo2_3',
				array(
						'value'=>'hello world',
						'time'=>date('Y-m-d H:i:s')
						)
				);
	}
	
	/**
	 * 一次引入多个视图
	 */
	function demo2_4(){
		
		$this->Render('demo2/demo2_4_1');
		
		//引入第一个视图
		$this->Render(
				'demo2/demo2_4_2',
				array(
						'value'=>'hello'
						)
				);
		//引入第二个视图
		$this->Render(
				'demo2/demo2_4_3',
				array(
						'value'=>'world'
						)
				);
		
		$this->Render('demo2/demo2_4_4');
	}
}
?>
<?php
/**
 * 模型(Model)基类
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * 模型基类。系统中所有模型对象均需要继承于此类。
 * 默认情况下，模型对象会从控制器对象(controller)中引入一些成员变量使用。包括：
 * 用户会话(session)，语言包配置(language)，通用配置(config)，输入对象(input)，插件对象(plugins)
 * -----------------------------------------------------------------------------------------------------------------------------------------------------------------*/
class  Model{
	
	//可从控制器(controller)中传入的对象属性
	private $__controller_member_key = array('session', 'language', 'config', 'input', 'plugins');
	
	/**
	 * 构造函数
	 * @param	controller object 控制器对象
	 * @param	param array 参数数组
	 */
	public function __construct($controller, $param=array()){
		//添加控制器中可用的属性
		foreach($this->__controller_member_key as $key){
			$this->$key = $controller->$key;
		}
	}
}
?>
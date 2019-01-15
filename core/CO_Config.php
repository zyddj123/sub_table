<?php
/**
 * 配置加载器
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * co应用个性化配置加载类
 * 适用于获取设置当前应用(application)下custom_config.php文件内容的配置。
 * ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
class CO_Config {
	
	protected  $_cfg_data;
	
	/**
	 *	构造函数
	 */
	function __construct(){
		//引入应用个性化配置文档
		include APP_CFG_PATH.'/custom_config.php';
		/*
		 * 遍历已经配置好的变量
		 * $custom_system_configs已经在custom_config.php中声明
		 */
		foreach ($custom_system_configs as $_key => $_cfg){
			$this->_cfg_data[$_key]=$_cfg;
		}
		//销毁$custom_system_configs
		unset($custom_system_configs);
	}
	
	/**
	 * 获取配置
	 * @param	key string 配置key
	 * @return	mixed
	 */
	function Get($key){
		if($key=='') return '';
		if(!is_array($this->_cfg_data) || !isset($this->_cfg_data[$key])) return '';
		return $this->_cfg_data[$key];
	}
	
	/**
	 * 全部属性
	 * @return	array 全部配置数据数组
	 */
	function All(){
		return $this->_cfg_data;
	}
	
	/**
	 * 设置配置
	 * 可以链式调用
	 * @param	key string 键值
	 * @param	value string 数值
	 * @return	this object
	 */
	function Set($key,$value){
		if ($key!=''){
			$this->_cfg_data[$key]=$value;
		}
		return $this;
	}
}

?>
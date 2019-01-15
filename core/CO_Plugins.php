<?php
/**
 * 开发插件类
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * 开发插件类。
 * 用于获取系统扩展插件对象。
 * ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

class CO_Plugins{
	//插件列表
	protected $_plugins=array();
	
	//系统内置非插件列表
	protected $_unplugin_class=array(
			'CO_Config',
			'CO_Input',
			'CO_Plugins',
			'CO_Session',
			'CO_Log',
			'Core',
			'Controller',
			'Model',
			'Output',
			'CO_DB_API',
			'CO_DB_Anaalyse',
			'CO_DB_Log',
			'CO_DB_Result',
			'CO_DB_Exception'
			);

	/**
	 * 是否包含扩展插件
	 * @param	plugins string 插件名称
	 * @return	boolean	
	 */
	function HasPlugin($plugins){
		//需要排除核心类
		if(in_array($plugins, $this->_unplugin_class)) return false;
		if (class_exists($plugins)){
			return true;
		}else return false;
	}
	
	/**
	 * 获取插件
	 * @param	plugins string 插件名称
	 * @param	arrParameters array 对象参数 
	 * @return	mixed 插件object对象,否则false
	 */
	function GetPlugin($plugins, $arrParameters=array()){
		
		if($this->HasPlugin($plugins)){
			if(array_key_exists($plugins, $this->_plugins)){
				//返回已经生成的对象
				return $this->_plugins[$plugins];
			}else{
				//使用反射机制实现插件对象实例化
				$objClass=new ReflectionClass($plugins);
				$objPlugin = $objClass->newInstanceArgs($arrParameters);
				//将插件对象放入_plugins数组中备用
				$this->_plugins[$plugins]=$objPlugin;
				return $objPlugin;
			}
		}else return false;
	}
}
?>
<?php
/**
 * 用户当前会话类
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * 用户会话类。管理当前基于$_SESSION的会话。
 * 在业务应用中可以开发集成于此类的个性化会话操作类。
 * ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
class CO_Session{
	
	protected $_session;
	
	/**
	 * 构造函数
	 */
	function __construct(){
		$this->_session=& $_SESSION;
	}
	
	/**
	 * 获取多维数组的内容
	 * @param	val array 多维数组
	 * @param	key string 数组key,如果多维key则使用":"分隔开
	 * @return	mixed 对应key的内容
	 */
	protected function _Get($array_value, $key){
		if($key=='') return null;
		$vKeys = explode(':', $key);
		for ($i = 0; $i < count($vKeys); $i++) {
			if($i==count($vKeys)-1){
				return $array_value[$vKeys[$i]];
			}else{
				if(!isset($array_value[$vKeys[$i]])) return null;
				$array_value=& $array_value[$vKeys[$i]];
			}
		}
	}
	
	/**
	 * 设置多维数组的内容
	 * @param	key string 数组key,如果多维key则使用":"分隔开
	 * @param	val mixed 设置的数值
	 * @return	boolean
	 */
	protected function _Set($key, $val){
		$tmp = & $this->_session;
		if($key==''){
			$tmp=$val;
		}else{
			$vKeys=explode(':', $key);
			for ($i = 0; $i < count($vKeys); $i++) {
				if($i==count($vKeys)-1){
					//终端节点,更新
					$tmp[$vKeys[$i]]=$val;
				}else{
					//遍历
					if(!isset($tmp[$vKeys[$i]])) return false;;
					$tmp=& $tmp[$vKeys[$i]];
				}
			}
		}
		return true;
	}
	
	/**
	 * 获取session值
	 * @param	key string 键值
	 * @return	mixed 数值
	 */
	function Get($key){
		return $this->_Get($this->_session, $key);
	}
	
	/**
	 * 设置session值。可以链式调用
	 * @param	key string 键值
	 * @param	val string 数值
	 * @return	object 当前对象
	 */
	function Set($key, $val){
		$this->_Set($key, $val);
		return $this;
	}
	
	/**
	 * 销毁会话
	 * @return	boolean
	 */
	function Destroy(){
		return session_destroy();
	}
}

?>
<?php
/**
 * 数据库查询结果集类
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * 查询结果集类
* SQL类数据库操作需要集成此基类实现
* ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

abstract class CO_DB_Result{
	//结果集数量
	protected $_result_num = 0;
	
	//结果集
	protected $_result = null;
	
	//结果集中的当前指针位移
	protected $_current_index = 0;
	
	/**
	 * 构造函数
	 * @param	result object 数据库结果
	 */
	function __construct($result){
		
	}
	
	/**
	 * 转化结果集成为数据数组
	 */
	public function GetData(){
		return true;
	}
	
	/**
	 * 获取指定位移的数据
	 * @param	index integer 位移下标
	 */
	public function GetNth($index){
		if($index>$this->_result_num || $index<0) return false;
		return $this->_GetNthData($index);
	}
	
	/**
	 * 获取下一个位移数据
	 */
	public function Next(){
		$index = $this->_current_index + 1;
		$data = $this->GetNth($index);
		if($data !== false) $this->_current_index = $index;
		return $data;
	}
	
	/**
	 * 获取上一个位移数据
	 */
	public function Previous(){
		$index = $this->_current_index - 1;
		$data = $this->GetNth($index);
		if($data !== false) $this->_current_index = $index;
		return $data;
	}
	
	/**
	 * 获取第一个位移数据
	 */
	public function First(){
		$index = 0;
		$data = $this->GetNth($index);
		if($data !== false) $this->_current_index = $index;
		return $data;
	}
	
	/**
	 * 获取最后一个位移数据
	 */
	public function Last(){
		$index = $this->_result_num;
		$data = $this->GetNth($index);
		if($data !== false) $this->_current_index = $index;
		return $data;
	}
}
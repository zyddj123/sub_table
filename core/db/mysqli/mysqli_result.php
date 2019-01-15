<?php
/**
 * mysqli数据库结果类
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * 继承自CO_DB_Result基类
* 扩展实现mysqli数据库的相应接口
* ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
class CO_DB_mysqli_result extends CO_DB_Result{
	
	/**
	 * 构造函数
	 * @param	result object mysql结果资源对象
	 */
	function __construct($result){
		parent::__construct($result);
		if(!is_bool($result)){
			$rows = array();
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				array_push($rows , $row);
			}
			if(count($rows)){
				$this->_result = $rows;
				$this->_result_num = count($rows);
			}
			$result->free();
		}else{
			$this->_result = $result;
			$this->_result_num = 1;
		}
		
	}
	
	/**
	 * 获取指定偏移量的数据
	 * @param	index integer 偏移量
	 * @return	array 数据数组
	 */
	protected function _GetNthData($index){
		if(!$this->_result || !isset($this->_result[$index])) return false;
		return $this->_result[$index];
	}
	
	/**
	 * 返回所有结果集的数据数组
	 * @return	mixed 结果集为空则返回null,否则返回数组array
	 */
	public function GetData(){
		return $this->_result==null?false:$this->_result;
	}
}
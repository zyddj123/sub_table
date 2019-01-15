<?php
/**
 * 数据库连接异常处理类
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * 数据库连接异常处理类
* SQL类数据库操作需要集成此基类实现
* ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

class CO_DB_Exception extends Exception{
	const DATABASE_NO_CONNNET_CODE = 1010001;
	const NO_DB_SELECT_CODE = 1010002;
	const SQL_QUERY_ERROR = 1010003;
	
	protected $_sql = '';
	protected $_sql_err_msg = '';
	
	protected function _SetSql($sql, $err_msg){
		$this->_sql = $sql;
		$this->_sql_err_msg = $err_msg;
	}
	
	public function GetSql(){
		return $this->_sql;
	}
	
	public function GetSqlErrMsg(){
		return $this->_sql_err_msg;
	}
	
	static function NoConnent($error_msg){
		throw new self($error_msg, self::DATABASE_NO_CONNNET_CODE);
	}
	
	static function NoDbSelected($error_msg){
		throw new self($error_msg, self::NO_DB_SELECT_CODE);
	}
	
	function QueryError($sql, $error_sql_msg){
		$this->_SetSql($sql, $error_sql_msg);
	}
}
<?php
/**
 * 数据库日志记录类
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * 数据日志记录类。
 * 继承自CO_Log
* ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

class CO_DB_Log extends CO_Log{
	
	protected $_log_line_separator = '--------------------';
	
	/**
	 * 构造函数
	 * @param	app_log_path string 应用单元内的数据库日志目录
	 */
	function __construct($app_log_path){
		parent::__construct($app_log_path);
	}
		
	function begin(){
		$log = '';
		$log .= '====================db log===================='.PHP_EOL;
		$log .= '[seq]'.PHP_EOL;
		$log .= microtime().PHP_EOL;
		$log .= $this->_log_line_separator.PHP_EOL;
		return $log;
	}
	
	function end(){
		//return '====================db log over===================='.PHP_EOL;
		//return PHP_EOL;
		return '';
	}
	
	function unmixed_query($sql_string, $sql_params){
		$log = '';
		$log .= '[unmixed_query]'.PHP_EOL;
		$log .= $sql_string.PHP_EOL;
		$log .= $this->_log_line_separator.PHP_EOL;
		$log .= '[unmixed_params]'.PHP_EOL;
		if(is_array($sql_params)) $log .= print_r($sql_params, true).PHP_EOL;
		else $log .= $sql_params.PHP_EOL;
		$log .= $this->_log_line_separator.PHP_EOL;
		return $log;
	}
	
	function mixed_query($sql_full_string){
		$log = '';
		$log .= '[full_sql]'.PHP_EOL;
		$log .= $sql_full_string.PHP_EOL;
		$log .= $this->_log_line_separator.PHP_EOL;
		return $log;
	}
	
	function query_result($result){
		$log = '';
		$log .= '[result]'.PHP_EOL;
		if(!is_string($result)) $log .= print_r($result, true);
		else $log .= $result;
		//$log .= PHP_EOL;
		return $log;
	}
	
	function query_error($error){
		$log = '';
		$log .= '[error]'.PHP_EOL;
		if(!is_string($error)) $log .= print_r($error, true);
		else $log .= $error;
		//$log .= PHP_EOL;
		return $log;
	}
	 
}
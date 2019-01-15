<?php
/**
 * 日志记录类
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * 日志记录类。
* ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

class CO_Log{
	
	protected $_log_file_path = '';						//日志文件路径
	protected $_log_file_max_size = '4096';				//单个日志文件最大容量,单位(Kb)
	static $Log_File_Suffix = 'log';				//日志文件后缀
	static $Log_File_Piece_Hyphen = '_';		//日志文件多片连接符
	
	/**
	 * 构造函数
	 */
	function __construct($log_file_path='', $log_file_max_size=''){
		if($log_file_path != '') $this->_log_file_path = $log_file_path;
		if($log_file_max_size != '') $this->_log_file_max_size = $log_file_max_size;
	}
	
	/**
	 * 日志记录
	 * @param	content string 日志内容
	 * @return	boolean
	 */
	public function write($content=''){
		if(is_array($content)) $content = print_r($content, true);
		$content = $content.PHP_EOL;
		$log_file = $this->getLogFile();
		if(!file_exists($log_file)) file_put_contents($log_file, $content);
		else{
			if(filesize($log_file) >= ($this->_log_file_max_size * 1000)){
				//超过单位日志文件容量上限,创建新的文件
				$new_log_file = $this->_log_file_path.'/'.static::GetLogFileName().static::$Log_File_Piece_Hyphen.strval($this->getMaxPiece(static::GetLogFileName())+1).'.'.static::$Log_File_Suffix;
				copy($log_file, $new_log_file);
				file_put_contents($log_file, $content);
			}else file_put_contents($log_file, $content, FILE_APPEND);
		}
		return true;
	}
	
	/**
	 * 获取当前日志文件的最大碎片数
	 * @param	file_base_name string 文件基准名称
	 * @return	int 最大数
	 */
	function getMaxPiece($file_base_name){
		$max = 0;
		$arr_not_exam = array('.', '..', $file_base_name.'.'.static::$Log_File_Suffix);
		if(is_dir($this->_log_file_path) && $dir = opendir($this->_log_file_path)){
			while (($file = readdir($dir)) !== false){
				$file_full_path = $this->_log_file_path.'/'.$file;
				if(is_file($file_full_path) && !in_array($file, $arr_not_exam)){
					$piece = str_replace($file_base_name, '', $file);
					$piece = str_replace('.'.static::$Log_File_Suffix, '', $piece);
					$piece = str_replace(static::$Log_File_Piece_Hyphen, '', $piece);
					if(intval($piece) > $max) $max = intval($piece);
				}
			}
		}
		return $max;
	}
	
	/**
	 * 获取日志基准名称,不包含扩展名
	 * @return	string 日志名称
	 */
	static function GetLogFileName(){
		return date('Y-m-d');
	}
	
	/**
	 * 获取日志文件的全路径地址
	 * @return	string 路径地址
	 */
	function getLogFile(){
		return $this->_log_file_path.'/'.static::GetLogFileName().'.'.static::$Log_File_Suffix;
	}	
}
?>
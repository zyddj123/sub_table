<?php
/**
 * 数据库连接接口类
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * 数据库连接接口类
 * SQL类数据库操作需要集成此基类实现
 * ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
include 'CO_DB_Result.php';
include 'CO_DB_Exception.php';

abstract class CO_DB_API{
	//上一条执行的sql语句
	protected $_last_query_sql = '';
	
	//上一条执行sql语句的耗时
	protected $_last_query_execute_time = '';
	
	//数据库连接对象
	protected $_link_identifier = null;
	
	//数据库结果集对象
	protected $_database_result = null;
	
	//数据库配置
	protected $_database_config = '';
	
	//数据库异常对象
	protected $_exception = null;
	
	//数据库异常信息
	protected $_exception_msg = '';
	
	//事务运行状态
	protected $_trans_status = true;
	
	//查询是否在事务中
	protected $_query_in_trans = false;
	
	protected static $Class_Prefix = 'CO_DB_';
	
	//字符集
	protected $_charset = '';
	
	//选中的数据库实例名称
	protected $_selected_db_name = '';
	
	//数据库日志
	protected $_db_logger = null;
	
	/**
	 * 构造函数
	 * @param	param array 参数
	 */
	function __construct($param){
		if(is_array($param) && count($param)){
			foreach($param as $key => $val){
				$this->_database_config[$key]=$val;
				switch (strtolower($key)) {
					case 'charset':
						$this->_charset = $val;				//字符集
						break;
					case 'db_name':
						$this->_selected_db_name = $val;				//使用的数据库实例名称
						break;
				}
			}
			if(isset($this->_database_config['log_type']) && $this->_database_config['log_type']!='' && isset($this->_database_config['log_path']) && $this->_database_config['log_path']!=''){
				$this->_db_logger = new CO_DB_Log($this->_database_config['log_path']);				//数据库日志
			}
		}
		
		$this->_exception = $this->_GetErrorClass();
	}
	
	/**
	 * 析构函数
	 */
	function __destruct(){
		$this->Close();
	}
	
	/**
	 * 初始化
	 * 完成数据库连接，选择数据实例，设置字符集
	 * @return	boolean
	 */
	protected function _Init(){
		if(!$this->Connect()){
			//数据库连接失败
			$this->_exception->NoConnent($this->_exception_msg);
			return false;
		}
		return true;
	}
	
	/**
	 * 运行完整的sql语句，并返回运行结果
	 * @param	sql string 完整的sql语句
	 * @return	boolean 是否运行成功
	 */
	protected function _ExecuteSql($sql, $data=array(), $param=array()){
		$this->Ping();				//确定连接持久有效
		
		$db_log_cotnent = '';
		
		if(!is_null($this->_db_logger)){
			$db_log_cotnent .= $this->_db_logger->begin();
			$db_log_cotnent .= $this->_db_logger->unmixed_query($sql, $data);
		}
		
		//对Sql语句进行预处理，合并通配符？及替换的数据数组。
		$full_sql = $this->_ProcessSql($sql, $data, $param);
		
		if(!is_null($this->_db_logger)){
			$db_log_cotnent .= $this->_db_logger->mixed_query($full_sql);
		}
		
		//开始记录语句运行时间
		$query_time = microtime(true);
		
		//调用子类实现的_Qurey方法实现查询并返回结果集
		$query_result = $this->_Query($full_sql);
		
		//运行完毕后计算本次操作的耗时，并且记录运行的sql语句
		$query_time = microtime(true) - $query_time;
		$this->_last_query_execute_time = $query_time;
		$this->_last_query_sql = $full_sql;
		
		if($query_result===false){
			//查询失败，记录查询错误信息并做响应处理
			
			if($this->_query_in_trans){
				//在事务中查询运行失败，需要设置回滚
				$this->_trans_status=false;
			}
			
			//记录查询错误信息及错误sql语句
			$this->_exception->QueryError($sql, $this->_exception_msg);
			
			if(!is_null($this->_db_logger)){
				$db_log_cotnent .= $this->_db_logger->query_error($this->_exception_msg);
				$db_log_cotnent .= $this->_db_logger->end($full_sql);
				$this->_db_logger->write($db_log_cotnent);
			}			
			return false;
		}else{
			//查询正确，实例化结果集对象并返回true
			$this->_database_result = $this->_GetResultClass($query_result);
			
			if(!is_null($this->_db_logger)){
				$db_log_cotnent .= $this->_db_logger->query_result($this->_database_result->GetData());
				$db_log_cotnent .= $this->_db_logger->end();
				if($this->_database_config['log_type']=='2') $this->_db_logger->write($db_log_cotnent);
			}
			return true;
		}		
	}
	
	/**
	 * 预处理需要运行的sql语句
	 * @param	sql string 查询语句，支持通配符？替代变量。
	 * @param	data array 与第一个参数sql配合使用。对应语句中需要替换的通配符？位置，？的数量需与data数组个数一致
	 * @return	string 处理完成的sql语句
	 */
	protected function _ProcessSql($sql, $data){
		return $sql;
	}
	
	/**
	 * 根据查询结果Resource实例化对应的结果集对象
	 * @param	result object 原生数据库查询结果
	 * @return	object 继承CO_DB_Result基类的子类对象
	 */
	protected function _GetResultClass($result){
		//判断参数是否有效
		if(!$result) return null;
		
		//根据type获取结果集类名称
		$class_file_name = $this->_database_config['type'].'_result';
		$class_name = static::$Class_Prefix.$this->_database_config['type'].'_result';
		
		//获取结果集类文件路径
		$class_file_path = realpath(__DIR__).'/'.$this->_database_config['type'].'/'.$class_file_name.'.php';
		
		//引入类文件，并生成实例
		include_once $class_file_path;
		return new $class_name($result);
	}
	
	/**
	 * 获取继承CO_DB_Exception基类的子类对象
	 * @return	object 继承CO_DB_Exception基类的子类对象
	 */
	protected function _GetErrorClass(){
		//根据type获取类名称
		$class_file_name = $this->_database_config['type'].'_exception';
		$class_name = static::$Class_Prefix.$this->_database_config['type'].'_exception';
		//获取类文件路径
		$class_file_path = realpath(__DIR__).'/'.$this->_database_config['type'].'/'.$class_file_name.'.php';
		
		//引入类文件，并生成实例
		include_once $class_file_path;
		return new $class_name();
	}
	
	/**
	 * 选择数据库实例
	 * @param	db_name string 实例名称
	 * @return	boolean 是否成功
	 */
	function SelectDb($db_name){
		$bln_return = true;
		$db_log_cotnent = '';				//日志
				
		if(!is_null($this->_db_logger)){
			$db_log_cotnent .= $this->_db_logger->begin();
			$db_log_cotnent .= $this->_db_logger->mixed_query('select db['.$this->_database_config['type'].'] on ['.$this->_database_config['user'].'@'.$this->_database_config['host'].':'.$this->_database_config['port'].']');
		}
		
		if($this->_selectDb($db_name)){
			$bln_return = true;
			
			if(!is_null($this->_db_logger)){
				$db_log_cotnent .= $this->_db_logger->query_result('success');
			}
			
		}else{
			$bln_return = false;
			
			if(!is_null($this->_db_logger)){
				$db_log_cotnent .= $this->_db_logger->query_error('failure');
			}			
		}
		
		if(!is_null($this->_db_logger)){
			$db_log_cotnent .= $this->_db_logger->end();
			if($this->_database_config['log_type'] == '2' || !$bln_return) $this->_db_logger->write($db_log_cotnent);
		}
		
		return $bln_return;
	}
	
	/**
	 * 创建数据库实例,并选择
	 * @param	db_name string 数据库实例
	 * @param	db_charset string 字符集
	 * @param	db_collation string 字符排序
	 * @param	bln_select_immeidately boolean 是否立即使用数据库
	 * @return boolean
	 */
	function CreateDb($db_name, $db_charset, $db_collation, $bln_select_immeidately=true){
		return $this->_createDb($db_name, $db_charset, $db_collation, $bln_select_immeidately);
	}
	
	/**
	 * 连接数据库
	 * @return	boolean 是否成功
	 */
	public function Connect(){
		$bln_return = true;
		$db_log_cotnent = '';				//日志
		
		if(!is_null($this->_db_logger)){
			$db_log_cotnent .= $this->_db_logger->begin();
			$db_log_cotnent .= $this->_db_logger->mixed_query('connect db['.$this->_database_config['type'].'] on ['.$this->_database_config['user'].'@'.$this->_database_config['host'].':'.$this->_database_config['port'].'] with password');
		}
		
		if($this->_connect()){
			//连接成功
			$bln_return = true;
			if(!is_null($this->_db_logger)){
				$db_log_cotnent .= $this->_db_logger->query_result('success');
			}
		}else{
			//连接失败
			$bln_return = false;
			if(!is_null($this->_db_logger)){
				$db_log_cotnent .= $this->_db_logger->query_error($this->_exception_msg);
			}
		}
		
		if(!is_null($this->_db_logger)){
			$db_log_cotnent .= $this->_db_logger->end();
			if($this->_database_config['log_type'] == '2' || !$bln_return) $this->_db_logger->write($db_log_cotnent);
		}
		
		return $bln_return;
	}
	
	/**
	 * 检测数据路连接
	 * @return	boolean
	 */
	public function Ping(){
		return $this->_ping();
	}
	
	/**
	 * 关闭数据库
	 * @return	boolean
	 */
	public function Close(){
		if($this->_link_identifier){
			$this->_close();
			$this->_link_identifier = null;
		}
		
		if(!is_null($this->_db_logger) && $this->_database_config['log_type'] == '2'){
			$db_log_cotnent = '';				//日志
			$db_log_cotnent .= $this->_db_logger->begin();
			$db_log_cotnent .= $this->_db_logger->mixed_query('close db['.$this->_database_config['type'].':'.$this->_database_config['db_name'].'] on ['.$this->_database_config['user'].'@'.$this->_database_config['host'].':'.$this->_database_config['port'].']');
			$db_log_cotnent .= $this->_db_logger->query_result('success');
			$db_log_cotnent .= $this->_db_logger->end();
			$this->_db_logger->write($db_log_cotnent);
		}
				
		return true;
	}
	
	/**
	 * 设置数据库连接字符集
	 * @param	charset string 字符集
	 * @return	boolean 是否成功
	 */
	public function SetCharset($charset){
		return $this->_setCharset($charset);
	}
	
	/**
	 * 输入Sql语句，进行查询并返回结果
	 * @param	sql string 查询语句，支持通配符？替代变量。
	 * @param	data array 与第一个参数sql配合使用。对应语句中需要替换的通配符？位置，？的数量需与data数组个数一致
	 * @param	param array 运行参数[可选]
	 * @return	mixed	如果查询正常则返回结果集对象的GetaData()函数，否则返回false
	 */
	public function Query($sql, $data=array(), $param=array()){
		//运行Sql语句
		$query_result = $this->_ExecuteSql($sql, $data, $param);
		//返回运行结果，如果查询失败则返回false
		if($query_result === false) return false;
		else{
			//查询正确，并且返回结果集数据数组
			return $this->_database_result->GetData();
		}
	}
	
	/**
	 * 获取sql运行错误
	 * @return	db_exception对象
	 */
	public function GetError(){
		return $this->_exception;
	}
	
	/**
	 * 获取查询耗时
	 * @return	float 耗时
	 */
	public function GetExcuteTime(){
		return $this->_last_query_execute_time;
	}
	
	/**
	 * 查询并获取结果集第一行
	 * @param	sql string 查询语句，支持通配符？替代变量。
	 * @param	data array 与第一个参数sql配合使用。对应语句中需要替换的通配符？位置，？的数量需与data数组个数一致。
	 */
	public function GetRow($sql, $data=array()){
		return true;
	}
	
	/**
	 * 查询并获取结果集数组
	 * @param	sql string 查询语句，支持通配符？替代变量。
	 * @param	data array 与第一个参数sql配合使用。对应语句中需要替换的通配符？位置，？的数量需与data数组个数一致。
	 */
	public function GetAll($sql, $data=array()){
		return true;
	}
	
	/**
	 * 更新数据条目
	 * @param	table string 表名
	 * @param	where array 查询条件数组
	 * @param	data array 更新字段数组
	 * @param	param array 参数数组[可选]
	 * @return	boolean 是否成功
	 */
	public function Update($table, $where, $data, $param=array()){
		return true;
	}
	
	/**
	 * 插入数据条目
	 * @param	table string 表名
	 * @param	data array 字段数组
	 * @param	param array 参数数组[可选]
	 * @param	mixed 表中如有自增(AutoIncrement)主键，则返回新增的主键id。如没有自增主键则返回boolean
	 */
	public function Insert($table, $data, $param=array()){
		return true;
	}
	
	/**
	 * 插入或更新条目
	 * 根据duplication key设置判断是更新操作还是插入操作
	 */
	public function InsertOrUpdate(){
		return true;
	}
	
	/**
	 * 删除条目
	 * @param	table string 表名
	 * @param	where array 查询条件数组[可选]
	 * @param	param array 更新字段数组[可选]
	 * @return	boolean 是否成功
	 */
	public function Delete($table, $where=array(), $param=array()){
		return true;
	}
	
	/**
	 * 选择查询条目
	 * @param	table string 表名
	 * @param	where array 查询条件数组[可选]
	 * @param	param array 更新字段数组[可选]
	 */
	public function Select($table, $where=array(), $param=array()){
		return true;
	}
	
	/**
	 * 事务起始
	 * @return	void
	 */
	function TransStart(){
		$this->_query_in_trans = true;
		$this->_trans_status = true;
		$this->_TransBegin();
	}
	
	/**
	 * 事务完成
	 * @return void
	 */
	function TransFinish(){
		if(!$this->_trans_status) $this->_TransRollback();
		else $this->_TransCommit();
		$ret = $this->_trans_status;
		$this->_trans_status = true;
		$this->_query_in_trans = false;
		return $ret;
	}
}
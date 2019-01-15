<?php
/**
 * mysqli数据库操作接口
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * 继承自CO_DB_API基类
 * 扩展实现mysqli数据库的相应接口
 * ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
class CO_DB_mysqli_api extends CO_DB_API{
	
	//static $insert_option = array('LOW_PRIORITY', 'DELAYED ', 'HIGH_PRIORITY');
	
	/**
	 * 构造函数
	 * @param	param array 参数
	 */
	function __construct($param){
		parent::__construct($param);
		if(!$this->_link_identifier) $this->_Init();
	}
		
	/**
	 * 连接数据库
	 * @return	boolean
	 */
	protected function _connect(){
		$this->_link_identifier = mysqli_init();
		$hostname = $this->_database_config['host'];
		if(isset($this->_database_config['persistent']) && $this->_database_config['persistent']) $hostname = 'p:'.$hostname;
		if(@$this->_link_identifier->real_connect(
				$hostname,
				$this->_database_config['user'],
				$this->_database_config['password'],
				$this->_database_config['db_name'],
				$this->_database_config['port'])){
			return true;
		}else{
			$this->_exception_msg = $this->_link_identifier->error;
			return false;
		}
	}
	
	/**
	 * 检测数据库连接
	 * @return	boolean
	 */
	protected function _ping(){
		if(!$this->_link_identifier->ping()){
			//mysqli has gone away, reconnect
			$this->Close();
			$this->_Init();
			$this->SetCharset($this->_charset);
			$this->SelectDb($this->_selected_db_name);
		}
		return true;
	}
	
	/**
	 * 关闭数据库
	 * @param	boolean true
	 */
	protected function _close(){
		@$this->_link_identifier->close();
		return true;
	}
	
	/**
	 * 查询语句
	 * @param	sql string 完整运行的Sql语句
	 * @return	mixed 失败返回false，正确返回结果
	 */
	protected function _Query($sql){
		if($sql==''){
			$this->_exception_msg = 'sql string is empty';
			return false;
		}
		if(!$this->_link_identifier) $this->_Init();
		$result = $this->_link_identifier->query($sql);
		if($result===false){
			$this->_exception_msg = $this->_link_identifier->error;
			return false;
		}
		return $result;
	}
	
	/**
	 * 选择数据库实例
	 * @param	db_name string 数据库实例名称
	 * @return	boolean
	 */
	protected function _selectDb($db_name){
		if(!$this->_link_identifier->select_db($db_name)){
			$this->_exception_msg =  $this->_link_identifier->error;
			$this->_exception->NoDbSelected($this->_exception_msg);			//抛出异常,指定的数据库实例非法
			return false;
		}else{
			$this->_selected_db_name = $db_name;
			return true;
		}
	}
	
	/**
	 * * 创建数据库实例,并选择
	 * @param	db_name string 数据库实例
	 * @param	db_charset string 字符集
	 * @param	db_collation string 字符排序
	 * @param	bln_select_immeidately boolean 是否立即使用数据库
	 * @return boolean
	 */
	protected function _createDb($db_name, $db_charset, $db_collation, $bln_select_immeidately=true){
		$query = 'CREATE DATABASE `'.$db_name.'` DEFAULT CHARACTER SET '.$db_charset.' COLLATE '.$db_collation;
		if($this->Query($query) !== false){
			//创建实例成功
			if($bln_select_immeidately){
				//选择此实例
				try {
					$this->SelectDb($db_name);
				} catch (Exception $e) {
					return false;
				}
			}
			return true;
		}return false;
	}
	
	/**
	 * 设置数据库字符集
	 * @param	$charset string 字符集
	 * @return	boolean
	 */
	protected function _setCharset($charset){
		if($this->_link_identifier->set_charset($charset) === true){
			$this->_charset = $charset;
			return true;
		}else return false;
	}
	
	/**
	 * 开启事务
	 * @return	boolean
	 */
	protected function _TransBegin(){
		$this->_link_identifier->autocommit(FALSE);
		return $this->_Query('START TRANSACTION');
	}
	
	/**
	 * 回滚事务
	 * @return	boolean
	 */
	protected function _TransRollback(){
		if ($this->_link_identifier->rollback()){
			$this->_link_identifier->autocommit(TRUE);
			return true;
		}
		return false;
	}
	
	/**
	 * 提交事务
	 * @return boolean
	 */
	protected function _TransCommit(){
		if ($this->_link_identifier->commit()){
			$this->_link_identifier->autocommit(TRUE);
			return true;
		}
		return false;
	}
	
	/**
	 * 预处理需要运行的sql语句
	 * @param	sql string 查询语句，支持通配符？替代变量。
	 * @param	data array 与第一个参数sql配合使用。对应语句中需要替换的通配符？位置，？的数量需与data数组个数一致[可选]
	 * @return	string 处理完成的sql语句
	 */
	protected function _ProcessSql($sql, $data=array()){
		if(is_array($data)){
			$sqlarr = explode('?', $sql);
			if(count($data) != count($sqlarr) - 1){
				$this->_exception_msg = 'input data not match sql';
				return false;
			}
			$temp_sql  = '';
			foreach($data as $i => $d){
				$temp_sql .= $sqlarr[$i];
				if(is_string($d)){
					//字符串类型
					$temp_sql .= "'" .  $this->_link_identifier->real_escape_string($d) . "'";
				}elseif(is_bool($d)){
					//布尔类型
					$temp_sql .= $d?"1":"0";
				}elseif($d === null){
					//null
					$temp_sql .= "NULL";
				}else{
					//其它
					$temp_sql .= $d;
				}
			}
			$temp_sql .= $sqlarr[count($data)];
		}
		return $temp_sql;
	}
	
	/**
	 * 预处理语句参数
	 * @param	data array 待处理的参数
	 * @return	array 处理完毕的结果数组
	 */
	protected function _ProcessDataParams($data){
		$field_list = array();
		$data_list = array();
		foreach($data as $field_name => $field_value){
			array_push($field_list, '`'.$field_name.'`');
			array_push($data_list, $field_value);
		}
		$placeholder = substr(str_repeat(",? ", count($field_list)), 1);
		return array(
				'field'=>implode(', ', $field_list),
				'holder'=>$placeholder,
				'data'=>$data_list
				);
	}
	
	/**
	 * 预处理where语句参数
	 * @param	data array 待处理的参数
	 * @return	array 处理完毕的结果数组
	 */
	protected function _ProcessWhereParams($data){
		$field_list = array();
		$data_list = array();
		foreach($data as $field_name => $field_value){
			array_push($field_list, '`'.$field_name.'`=?');
			array_push($data_list, $field_value);
		}
		return array(
				'field'=>implode(' AND ', $field_list),
				'data'=>$data_list
		);
	}
	
	/**
	 * 预处理update语句中的参数
	 * @param	data array 待处理的参数
	 * @return	array 处理完毕的结果数组
	 */
	protected function _ProcessUpdateParams($data){
		$field_list = array();
		$data_list = array();
		foreach($data as $field_name => $field_value){
			array_push($field_list, '`'.$field_name.'`=?');
			array_push($data_list, $field_value);
		}
		return array(
				'field'=>implode(', ', $field_list),
				'data'=>$data_list
		);
	}
	
	/**
	 * 获取上次插入的自增主键
	 * @return	integer
	 */
	protected function _InsertId(){
		return $this->_link_identifier->insert_id;
	}
	
	/**
	 * select语句查询
	 * @param	table string 表名
	 * @param	where array where条件数组[可选]
	 * @param	param array 参数数组[可选]。键值包括筛选字段(select)，排序字段(orderby)，显示个数(limit)
	 * @return	boolean 是否查询成功
	 */
	protected function _Select($table, $where=array(), $param=array()){
		if(!$table) return false;
		$sql = 'SELECT ';
		$param_data = array();
		//-----------------------------------select fields------------------------------------//
		$select_field_list = array();
		if(isset($param)){
			if(is_array($param['select'])){
				$select_fields = $param['select'];
				foreach($select_fields as $name => $alias){
					if(is_numeric($name)){
						//无别名
						array_push($select_field_list, '`'.$alias.'`');
					}else{
						//存在别名
						if(strtolower($name) == 'count(*)') array_push($select_field_list, $name.' AS `'.$alias.'`');
						else	 array_push($select_field_list, '`'.$name.'` AS `'.$alias.'`');
					}
				}
			}elseif(is_string($param['select'])){
				$select_field_list = array($param['select']);
			}
		}
		if(count($select_field_list)>0) $sql .= implode(', ', $select_field_list);
		else $sql .= '*';
		//-----------------------------------from table------------------------------------//
		$sql .= ' FROM '.$table;
		//-----------------------------------where-----------------------------------------//
		if(count($where)){
			$processed_where = $this->_ProcessWhereParams($where);
			$sql .= ' WHERE '.$processed_where['field'];
			$param_data = array_merge($param_data, $processed_where['data']);
		}
		//-----------------------------------order by---------------------------------------//
		$order_field_list = array();
		if(isset($param) && is_array($param['orderby'])){
			$orderby = $param['orderby'];
			foreach($orderby as $field => $order){
				if(is_numeric($field)){
					//未指定排序方式
					array_push($order_field_list, '`'.$order.'`');
				}else{
					//指定排序方式
					array_push($order_field_list, '`'.$field.'` '.$order);
				}
			}
		}
		if(count($order_field_list)) $sql .= ' ORDER BY '.implode(', ', $order_field_list);
		//-----------------------------------limit-------------------------------------------//
		if(isset($param)  && is_array($param['limit'])){
			$limit_count = $param['limit'][0];
			if(!isset($param['limit'][1])) $limit_start = 0;
			else $limit_start = $param['limit'][1];
			$sql .= ' LIMIT '.$limit_start.','.$limit_count;
		}
		
		if($this->_ExecuteSql($sql, $param_data) === true){
			return true;
		}else return false;
	}
	
	/**
	 * insert语句 
	 * @param	table string 表名
	 * @param	data array 字段数组
	 * @param	param array 参数数组[可选]
	 * @param	mixed 表中如有自增(AutoIncrement)主键，则返回新增的主键id。如没有自增主键则返回boolean
	 */
	function Insert($table, $data, $param=array()){
		if(!$table || !is_array($data)) return false;
		//预处理数据
		$processed_data = $this->_ProcessDataParams($data);
		$sql = 'INSERT INTO `'.$table.'` ';
		$sql.= '('.$processed_data['field'].') VALUES ('.$processed_data['holder'].')';
		if($this->_ExecuteSql($sql, $processed_data['data']) === true){
			return $this->_InsertId()==0?true:$this->_InsertId();
		}else return false;
	}
	
	/**
	 * update语句
	 * @param	table string 表名
	 * @param	where array 查询条件数组
	 * @param	data array 更新字段数组
	 * @param	param array 参数数组[可选]
	 * @return	boolean 是否成功
	 */
	function Update($table, $where, $data, $param=array()){
		if(!$table || !is_array($data)) return false;
		$sql = 'UPDATE '.$table;
		$param_data = array();
		//预处理数据
		$processed_data = $this->_ProcessUpdateParams($data);
		$sql.= ' SET '.$processed_data['field'];
		$param_data = array_merge($param_data, $processed_data['data']);
		if(count($where)){
			$processed_where = $this->_ProcessWhereParams($where);
			$sql .= ' WHERE '.$processed_where['field'];
			$param_data = array_merge($param_data, $processed_where['data']);
		}
		if($this->_ExecuteSql($sql, $param_data) === true){
			return true;
		}else return false;
	}
	
	/**
	 * delete语句
	 * @param	table string 表名
	 * @param	where array 查询条件数组[可选]
	 * @param	param array 更新字段数组[可选]
	 * @return	boolean 是否成功
	 */
	function Delete($table, $where=array(), $param=array()){
		if(!$table) return false;
		$sql = 'DELETE FROM '.$table;
		$param_data = array();
		if(count($where)){
			$processed_where = $this->_ProcessWhereParams($where);
			$sql .= ' WHERE '.$processed_where['field'];
			$param_data = array_merge($param_data, $processed_where['data']);
		}
		if($this->_ExecuteSql($sql, $param_data) === true){
			return true;
		}else return false;
	}
	
	/**
	 * select语句，返回所有符合结果数据数组
	 * @param	table string 表名
	 * @param	where array where条件数组[可选]
	 * @param	param array 参数数组[可选]。键值包括筛选字段(select)，排序字段(orderby)，显示个数(limit)
	 * @return	mixed 有查询结果则返回结果集数组，否则返回false
	 */
	function Select($table, $where=array(), $param=array()){
		if($this->_Select($table, $where, $param)) return $this->_database_result->GetData();
		else return false;
	}
	
	/**
	 * select语句，返回所有符合结果数据数组中的第一条
	 * @param	table string 表名
	 * @param	where array where条件数组[可选]
	 * @param	param array 参数数组[可选]。键值包括筛选字段(select)，排序字段(orderby)，显示个数(limit)
	 * @return	mixed 有查询结果则返回结果集中的第一条记录，否则返回false
	 */
	function SelectOne($table, $where=array(), $param=array()){
		if($this->_Select($table, $where, $param)) return $this->_database_result->First();
		else return false;
	}
	
	/**
	 * 运行Sql语句，返回结果集合数组中的第一条
	 * @param	sql string sql语句
	 * @param	data array sql参数数组[可选]
	 * @return	mixed 有查询结果则返回结果集中的第一条记录，否则返回false
	 */
	function GetRow($sql, $data=array()){
		if($sql=='') return false;
		if($this->_ExecuteSql($sql, $data) === true){
			return $this->_database_result->First();
		}else return false;
	}
	
	/**
	 * 旧版本代码兼容
	 * 返回查询结果集合的第一条目的第一个字段数值
	 * @return	mixed,查询到结果返回string,否则false
	 */
	function GetOne($sql, $data=array()){
		$query = $this->GetRow($sql, $data);
		if($query === false) return false;
		foreach ($query as $key => $val){
			return $val;
		}
	}
	
	/**
	 * 运行Sql语句，返回结果集合数组
	 * @param	sql string sql语句
	 * @param	data array sql参数数组[可选]
	 * @return	数组
	 */
	function GetAll($sql, $data=array()){
		if($sql=='') return array();
		if($this->_ExecuteSql($sql, $data) === true){
			$arrRet = $this->_database_result->GetData();
			if($arrRet === false) return array();
			else return $arrRet;
		}else return array();
	}
}
?>
<?php
/**
 * 数据表格类
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * CO数据表格类
 * 用于操作数据库内单表数据。提供增加，更新删除操作。
 * 成员变量$_co_dataform_table指数据库中的数据表。
 * 成员变量$_co_dataform_cfg_field指数据表中参与运转的字段。
 * 成员变量$_co_dataform_main_key指数据库表中的主键，可以是数组，也可以是字符串。
 * 
 * 请注意，此类涉及数据库操作。必须放在CO框架内使用。
 * ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
abstract class CO_DataForm{
	
	//数据表配置
	protected static $_co_dataform_table = null;
	
	//数据表字段配置
	protected static $_co_dataform_field = null;
	
	//数据表主键配置
	protected static $_co_dataform_main_key = null;
	
	//数据表主键值
	protected $_co_dataform_main_key_value = null;
	
	//数据库实例
	protected  static $_co_dataform_db_name = '';
	
	//数据值的数组，为了避免占用子类的成员变量，将所有数据字段名称和值按照key-value的方式放入此数组
	protected $_co_dataform;
	
	/**
	 * 设置数据库实例
	 */
	protected static function _SetCODataForm($db_name){
		static::$_co_dataform_db_name = $db_name;
	}
	
	/**
	 * 获取属性
	 */
	public function GetProp($field_name){
		if($field_name=='' || !isset($this->_co_dataform[$field_name])) return '';
		return $this->_co_dataform[$field_name];
	}
	
	/**
	 * 加载数据表中的记录,并且初始化对象属性
	 * @return	boolean 是否加载成功
	 */
	protected function _CODataformLoad(){
		if(!$this->__COCheckValid()) return false;
		try {
			$db = GetDB(static::$_co_dataform_db_name);
			/* ---------------------------------------------------------------------------------------------------------------------------
			 * 根据主键进行查找
			 * ---------------------------------------------------------------------------------------------------------------------------*/
			$where = array();
			if(is_array($this->_co_dataform_main_key_value)){
				//主键是数组
				foreach($this->_co_dataform_main_key_value as $field_name => $field_value){
					$where[$field_name]=$field_value;
				}
			}else{
				//主键是字符串
				$where[static::$_co_dataform_main_key]=$this->_co_dataform_main_key_value;
			}
			$query = $db->SelectOne(static::$_co_dataform_table, $where, array('select'=>static::$_co_dataform_field));
			/* ---------------------------------------------------------------------------------------------------------------------------
			 * 将结果放入co_dataform中，放入前需要运行_co_dataform_data_transform_from方法进行数据转化
			* ---------------------------------------------------------------------------------------------------------------------------*/
			if($query === false){
				//无结果，返回false
				return false;
			}else{
				foreach($query as $key => $val){
					//从数据库转化到成员变量
					$this->_co_dataform[$key] = $this->__CODataformDataTransformFrom($key, $val);
				}
			}
		} catch (CO_DB_Exception $e) {
			//接收数据库异常
			return false;
		}
		return true;
	}
		
	/**
	 * 创建数据库记录
	 * @param	data array 数据库记录
	 * @return	mixed 创建成功后的主键id,创建失败返回false
	 */
	protected static function _CODataformAdd($data){
		//生成更新数据库用的数组
		$_update_data=array();
		foreach (static::$_co_dataform_field as $_field){
			if(array_key_exists($_field, $data)){
				$_update_data[$_field]=$data[$_field];
			}
		}
		//没有合法的更新数据,则退出
		if(count($_update_data)==0) return false;
		//插入字段
		try {
			$db=GetDB(static::$_co_dataform_db_name);
			return $db->Insert(static::$_co_dataform_table, $_update_data);
		} catch (CO_DB_Exception $e) {
			//接收数据库异常
			return false;
		}
	}
	
	/**
	 * 更新数据表字段
	 * @param	data array 传入更新的数据数组.key与数据表中字段一致
	 * @return	boolean 是否更新成功
	 */
	protected function _CODataformUpdate($data){
		if(!$this->__COCheckValid()) return false;
		//生成更新数据库用的数组
		$_update_data=array();
		foreach (static::$_co_dataform_field as $_field){
			if(array_key_exists($_field, $data)){
				$_update_data[$_field]=$this->__CODataformDataTransformTo($_field, $data[$_field]);
			}
		}
		//没有合法的更新数据,则退出
		if(count($_update_data)==0) return false;
		//更新数据库字段 
		try {
			$db=GetDB(static::$_co_dataform_db_name);
			$where = array();
			if(is_array($this->_co_dataform_main_key_value)){
				//主键是数组
				foreach(static::$_co_dataform_main_key as $field_name){
					$where[$field_name]=$this->_co_dataform_main_key_value[$field_name];
				}
			}else{
				//主键是字符串
				$where[static::$_co_dataform_main_key]=$this->_co_dataform_main_key_value;
			}
			if($db->Update(static::$_co_dataform_table, $where, $_update_data) === false){
				//更新失败
				return false;
			}
			//同时设置更新对象属性
			foreach ($_update_data as $key => $val){
				$this->_co_dataform[$key]=$data[$key];
			}
		} catch (Exception $e) {
			//接收数据库异常
			return false;
		}
		return true;
	}
	
	/**
	 * 删除数据记录
	 * 删除数据表中对应的记录条目
	 * 注销当前对象中与数据表字段对应的属性
	 * 当前对象的唯一id(_co_dataform_main_key_value)置空
	 * @return	boolean 删除是否成功
	 */
	protected function _CODataformDelete(){
		if(!$this->__COCheckValid()) return false;
		try {
			$db=GetDB(static::$_co_dataform_db_name);
			$where = array();
			if(is_array($this->_co_dataform_main_key_value)){
				//主键是数组
				foreach(static::$_co_dataform_main_key as $field_name){
					$where[$field_name]=$this->_co_dataform_main_key_value[$field_name];
				}
			}else{
				//主键是字符串
				$where[static::$_co_dataform_main_key]=$this->_co_dataform_main_key_value;
			}
			if($db->Delete(static::$_co_dataform_table, $where) === false){
				//删除不成功
				return false;
			}else{
				//删除成功，清除对象内置属性
				$this->_co_dataform = null;
				$this->_co_dataform_main_key_value = null;
			}
		} catch (Exception $e) {
			//接收数据库异常
			return false;
		}
		return true;
	}
	
	/**
	 * 将对象属性数据,转化为数据表中可存储的原生数据
	 * @param	field_name string 表字段名称
	 * @param	undeal_data mixed 未转化的数据
	 * @return	mixed 转化后的数据
	 */
	private function __CODataformDataTransformTo($field_name, $undeal_data){
		if ($field_name=='') return false;
		return $this->__CODataformDataTransform('_co_dataform_dt_to_'.$field_name ,$undeal_data);
	}
	
	/**
	 * 从数据表中提取原生数据后,转化为对象属性
	 * @param	field_name string 表字段名称
	 * @param	undeal_data string 未转化的数据
	 * @return	mixed 转化后的数据
	 */
	private function __CODataformDataTransformFrom($field_name, $undeal_data){
		if($field_name=='') return false;
		return $this->__CODataformDataTransform('_co_dataform_dt_from_'.$field_name ,$undeal_data);
	}
	
	/**
	 * 运行数据转化
	 * @param	func_name string 函数名称
	 * @param	undeal_data string 待处理数据
	 * @return	string 处理后的数据
	 */
	private function __CODataformDataTransform($func_name, $undeal_data){
		if(method_exists($this, $func_name)){
			return $this->$func_name($undeal_data);
		}else{
			return $undeal_data;
		}
	}
	
	/**
	 * 判断co_dataform各个字段配置是否合理
	 */
	private function __COCheckValid(){
		//数据表不能为空
		if(!static::$_co_dataform_table) return false;
		//main_key与main_key_value类型要一致
		if(is_array(static::$_co_dataform_main_key) && !is_array($this->_co_dataform_main_key_value)) return false;
		if(!is_array(static::$_co_dataform_main_key) && is_array($this->_co_dataform_main_key_value)) return false;
		if(is_array(static::$_co_dataform_main_key) && is_array($this->_co_dataform_main_key_value) && count(static::$_co_dataform_main_key)!=count($this->_co_dataform_main_key_value)) return false;
		//数据字段不能为空
		if(!is_array(static::$_co_dataform_field) || count(static::$_co_dataform_field)==0) return false;
		return true;
	}
}
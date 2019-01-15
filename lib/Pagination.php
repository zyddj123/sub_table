<?php
/**
 * mw_数据条目查询分页器
 *
 * @package
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */
class PaginationException extends Exception{}

class Pagination{
	
	protected $_db = null;																				//数据库连接
	
	public $ppc =20 ;																						//每页显示条目
	public $count = 0;																					//总条目数
	public $page = 0;																						//页码数
	public $current= 0;																					//当前页码数
	
	protected  $query;																							//当前页查询结果
	protected $sql_after_from = '';																		//from字段之后(包含from)的sql语句片段,不包含limit子句
	protected $sql_select_fields='';																		//选择字段
	protected $sql_value_array = array();															//sql变量数组
	protected $sql_order = '';																				//ordr子句
	
	/**
	 * 构造函数
	 * @param	table string 数据表
	 * @param	after_table array sql子句,从表名之后的部分
	 */
	function __construct($table, $where, $order, $settings){
		try {
			$this->_db = GetDB();
		} catch (Exception $e) {}
		//from 表
		$this->sql_after_from = 'FROM '.$table;
		//where 条件
		if(isset($where['sql']) && $where['sql']!=''){
			$this->sql_after_from .= ' WHERE '.$where['sql'];
			$this->sql_value_array = $where['value'];
		}
		//单页显示条目
		if(isset($settings['ppc']) && isset($settings['ppc'])>0) $this->ppc=$settings['ppc'];
		//查询下总数
		$query=$this->_db->Query('SELECT COUNT(*) AS COUNT '.$this->sql_after_from,$this->sql_value_array);
		if($query){$this->count=intval($query[0]['COUNT']);
		}else $this->count=0;
		//总页码数
		if($this->count % $this->ppc >0){
			$this->page=(int)($this->count/$this->ppc)+1;
		}else{
			$this->page=(int)($this->count/$this->ppc);
		}		
		//排序
		if($order!='') $this->sql_order=' ORDER BY '.$order;
		//参数,选择字段
		if(count($settings['selectfields'])>0){
			foreach($settings['selectfields'] as $val){
				$this->sql_select_fields==''?$this->sql_select_fields=$val:$this->sql_select_fields.=', '.$val;
			}	
		}else $this->sql_select_fields='*';
		//参数,每页显示条目
		if(isset($settings['ppc']) && is_numeric($settings['ppc'])){
			$this->ppc=$settings['ppc'];
		}
	}
	
	/**
	 * 翻到指定页码
	 * @param	page int 页码数
	 * @return	当前对象
	 */
	public function goPage($page=0){
		$page=intval($page);
		if($page<=0) return $this;
		//不得超过最大页码
		if($page>$this->page) return $this;
		//计算起始和结束页码
		$start=($page-1) * $this->ppc;
		//生成sql语句
		$sql = 'SELECT '.$this->sql_select_fields.' '.$this->sql_after_from.$this->sql_order.' LIMIT '.$start.','.$this->ppc;
		$this->query=$this->_db->Query($sql,$this->sql_value_array);
		$this->current=$page;
		return $this;
	}
	
	/**
	 * 上一页
	 * @return	当前对象
	 */
	public function prev(){
		return $this->goPage($this->current-1);
	}
	
	/**
	 * 下一页
	 * @return	当前对象
	 */
	public function next(){
		return $this->goPage($this->current+1);
	}
	
	/**
	 * 首页
	 * @return	当前对象
	 */
	public function first(){
		return $this->goPage(1);
	}
	
	/**
	 * 尾页
	 * @return	当前对象
	 */
	public function last(){
		return $this->goPage($this->page);
	}
	
	/**
	 * 获取当前页的数据
	 * @return	array 数据库记录
	 */
	public function getInfo(){
		return $this->query;
	}
	
	/**
	 * 是否有下页
	 * @return	boolean
	 */
	public function hasMore(){
		if($this->count==0) return false;
		if($this->current==0) return true;
		if($this->current==$this->page) return false;
		else return true;
	}
}
?>
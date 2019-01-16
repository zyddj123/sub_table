<?php
//非法访问
if (!defined('BASECHECK')){
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
	exit;
}

/**
 * 按月份 分表 插入、查询 数据
 * 
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2017 - 2019.
 * @license
 * @link
 * @since				Version 1.17
 *
 */
class Demo1Controller extends Controller{
	private $db='';

	protected function _init(){
		$this->db = GetDB();
	}
	
	function run(){

	}

	//分表插入数据
	function insertData(){
		$month = date("Y-m");
		// $month = "2019-01";
		$table = 'data'.$month;
		// var_dump(strtotime("2019-01-15"));die;
		$data = array('name'=>'zhangsan','visit_time'=>date('Y-m-d'));
		$flag = $this->db->query("show tables like '{$table}'");
		// var_dump($flag);
		if($flag){
			//有此表则插入数据
			$this->db->insert($table,$data);
		}else{
			//没有此表则创建此表
			$sql = "CREATE TABLE `{$table}` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`name` varchar(255) NOT NULL,
					`visit_time` date NOT NULL,
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB AUTO_INCREMENT=201901151 DEFAULT CHARSET=utf8;";
			$sta = $this->db->query($sql);
			var_dump($sta);
		}
		
	}

	//分表读取数据
	function readData(){
		$prefix = "data";     				//表前缀  例如 表名称为 data2019-01 
		$t_start = "2018-10-01";  			//要查询的开始时间	
		$t_end = "2019-01-18";				//要查询的结束时间	
		$start_month = date('Y-m',strtotime($t_start));  //开始月份
		$end_month = date('Y-m',strtotime($t_end));      //结束月份
		$monthArr = $this->dateMonths($start_month,$end_month);  //开始结束月份之间的月份数组
		var_dump($monthArr);
		$sql = "";
		foreach ($monthArr as $key => $value) {
			//判断是否存在这张表  存在继续  不存在 跳出本次循环
			if(!$this->db->query("show tables like '".$prefix.$value."'")){continue;}

			//采用union联合查询查询数据  判断是否是查询第一个月份数组数据
			if($sql==""){
				$sql .= "select name, visit_time from `".$prefix.$value."` WHERE `visit_time` BETWEEN '{$t_start}' AND '{$t_end}' ";	
			}else{
				$sql .= " UNION ALL select name, visit_time from `".$prefix.$value."` WHERE `visit_time` BETWEEN '{$t_start}' AND '{$t_end}' ";
			}
		}

		$res = false;
		if($sql!=""){
			$res = $this->db->getAll($sql);
		}
		var_dump($res);
	}

	/**
	 * 计算出两个日期之间的月份
	 * @author ERIC
	 * @param  [type] $start_date [开始日期，如2018-03]
	 * @param  [type] $end_date   [结束日期，如2019-01]
	 * @return [type]             [返回是两个月份之间所有月份字符串]
	 */
	function dateMonths($start_date,$end_date){
		//判断两个时间是不是需要调换顺序
		$start_int = strtotime($start_date);
		$end_int = strtotime($end_date);
		if($start_int>$end_int){
			$tmp = $start_date;
			$start_date = $end_date;
			$end_date = $tmp;
		}
		//结束时间月份+1，如果是13则为新年的一月份
		$start_arr = explode('-',$start_date);
		$start_year = intval($start_arr[0]);
		$start_month = intval($start_arr[1]);

		$end_arr = explode('-',$end_date);
		$end_year = intval($end_arr[0]);
		$end_month = intval($end_arr[1]);

		$data = array();
		$data[] = $start_date;

		$tmp_month = $start_month;
		$tmp_year = $start_year;

		//如果起止不相等，一直循环
		while (!(($tmp_month == $end_month) && ($tmp_year == $end_year))) {
			$tmp_month ++;
			//超过十二月份，到新年的一月份
			if($tmp_month > 12){
				$tmp_month = 1;
				$tmp_year++;
			}
			$data[] = $tmp_year.'-'.str_pad($tmp_month,2,'0',STR_PAD_LEFT);
		}
		return $data;
	}


	
}
?>
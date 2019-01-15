<?php
//非法访问
if (!defined('BASECHECK')){
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
	exit;
}

/**
 * 演示Demo Lesson 1，包括：
 * 分表 插入、查询 数据
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
		$table = 'data'.$month;
		$data = array('name'=>'zhangsan','visit_time'=>date('Y-m-d'));
		$flag = $this->db->query("show tables like '{$table}'");
		// var_dump($flag);
		if($flag){
			//有此表则插入数据
			$this->db->insert($table,$data);
		}else{
			//没有此表则创建此表

		}
		
	}

	//分表读取数据
	function readData(){
		$t_start = "2018-12-15";
		$t_end = "2019-01-13";
		$sql = 'select * from `data2018-12` WHERE visit_time BETWEEN "2018-12-10" AND "2019-01-20" UNION select * from `data2019-01` WHERE visit_time BETWEEN "2018-12-10" AND "2019-01-20"';
		$res = $this->db->getAll($sql);
		var_dump($res);
	}
	
}
?>
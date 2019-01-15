<?php
//非法访问
if (!defined('BASECHECK')){
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
	exit;
}

/**
 * 用户模型
 * 
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 *
 */
class UserModel extends Model{
	
	/**
	 * 构造函数
	 * @param	controller object 控制器对象
	 * @param	param array 参数数组[可选]
	 */
	function __construct($controller, $param=array()){
		parent::__construct($controller, $param);
	}
	
	
	/**
	 * 获取用户信息
	 * @return	array 用户数组
	 */
	function getUserInfo(){
		return array(
				'name'=>'张三',
				'id'=>'zhangsan'
				);
	}
}

?>
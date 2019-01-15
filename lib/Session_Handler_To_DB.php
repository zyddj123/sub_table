<?php
//非法访问
if (!defined('BASECHECK')){
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
	exit;
}
/**
 * 自定义会话管理
 * 将会话SESSION数据存放在数据库红
 *
 * @package
 * @author			B.I.T
 * @copyright	Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since
 * @since				Version 1.17
 */
class Session_Handler_To_DB implements SessionHandlerInterface{
	
	//会话生命周期
	protected $_lifetime='7200';
	
	//session table
	protected $_table='';

	/**
	 * 构造函数
	 * @param	arrSettings array 配置参数[可选]
	 */
	function __construct($arrSettings=array()){
		if(is_numeric($arrSettings['lifetime'])) $this->_lifetime=$arrSettings['lifetime'];
	}
	
	/**
	 * SESSION打开
	 * @param	save_path	string 保存路径
	 * @param	session_name string 会话id
	 * @return	boolean 是否成功
	 */
	public function open($savePath, $sessionName){
        return true;
    }
    
    /**
     * SESSION关闭
     * @return	boolean
     */
    public function close(){
    	return true;
    }
    
    /**
     * 读取SESSION信息并验证是否有效
     * @param	key string session的key值
     * @return	mixed
     */
    public function read($key){
    	try {
    		$db = GetDB();
    		$current_time = time();
    		/* ------------------------------------------------------------------------------------------------------------------------------
    		 * 首先删除当前key下已经过期的session
    		 * ------------------------------------------------------------------------------------------------------------------------------*/
    		//$db->Query('DELETE FROM '.$this->_table.' WHERE SESS_KEY=? AND EXPIRY_DATE<?', array($key, $current_time));
    		/* ------------------------------------------------------------------------------------------------------------------------------
    		 * 查询当前key下未超时的Session
    		* ------------------------------------------------------------------------------------------------------------------------------*/
    		$query = $db->Query("SELECT SESS_VALUE FROM ".$this->_table.' WHERE SESS_KEY=? AND EXPIRY_DATE >=?', array($key, $current_time));
    		/* ------------------------------------------------------------------------------------------------------------------------------
    		 * 返回结果SESS_VALUE
    		* ------------------------------------------------------------------------------------------------------------------------------*/
    		if(is_array($query) && count($query)>0){
    			return $query[0]['SESS_VALUE'];
    		}else return false;
    		
    	} catch (CO_DB_Exception $e) {
    		//接收数据库异常
    		return false;
    	}
    }
    
    /**
     * 写入SESSION信息
     * @param	key string session的key值
     * @param	val string session数值
     * @return	boolean
     */
    public function write($key, $val){
    	try {
    		$db = GetDB();
    		//获取远程操作IP
    		$ip = bindec(decbin(ip2long($_SERVER['REMOTE_ADDR'])));
    		$current_time = time();
    		/* ------------------------------------------------------------------------------------------------------------------------------
    		 * 刷新过期时间，并插入session记录
    		 * ------------------------------------------------------------------------------------------------------------------------------*/
    		$new_expriy = $current_time + $this->_lifetime;
    		if($db->Select($this->_table, array('SESS_KEY' => $key), array('select'=>array(SESS_KEY))) === false){
    			$ins = $db->Insert($this->_table, array(
    					"SESS_KEY"=>$key,
    					"SESS_VALUE"=>$val,
    					"EXPIRY_DATE"=>$new_expriy,
    					"LOGIN_IP"=>$ip
    			));
    		}else{
    			//如果session已经存在，只更新过期时间
    			$ins = $db->Query('UPDATE '.$this->_table.' SET `EXPIRY_DATE`=?, `SESS_VALUE`=?, `LOGIN_IP`=? WHERE `SESS_KEY`=?', array($new_expriy, $val, $ip, $key));
    		}
    		return $ins===false?false:true;
    	} catch (Exception $e) {
    		//接收数据库异常
    		return false;
    	}
    }
    
    /**
     * 删除Session信息
     * @param	key string Session的key值
     * @return	boolean
     */
    public function destroy($key){
    	try {
    		$db=GetDB();
    		$db->Delete($this->_table, array('SESS_KEY'=>$key));
    		return true;
    	} catch (Exception $e) {
    		//接收数据库异常
    		return false;
    	}
    }
    
    /**
     * 回收超时SESSION信息
     * @param
     * @return boolean
     */
    public function gc($maxlifetime){
    	try {
    		$db=GetDB();
    		$db->Query('DELETE FROM '.$this->_table.' WHERE `EXPIRY_DATE`<?', array(time()));
    	} catch (Exception $e) {
    		//接收数据库异常
    	}
    	return true;
    }
}
?>

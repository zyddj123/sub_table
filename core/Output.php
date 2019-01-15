<?php
/**
 * 页面输出类
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * 页面输出类。
 * 控制器对象可以绑定页面输出类对象，并将视图中的内容显示出来。
 * -----------------------------------------------------------------------------------------------------------------------------------------------------------------*/
class Output {
	
	protected $_output;
	
	/**
	 * 构造函数
	 */
	function __construct(){
		
	}
	
	/**
	 * 设置输出内容
	 * @param	output string 输出字符串
	 * @return	void
	 */
	function SetOutputStream($output) {
		$this->_output = $output;
	}
	
	/**
	 * 获取输出内容
	 * @return	void;
	 */
	function GetOutputStream() {
		return $this->_output;
	}
	
	/**
	 * 追加输出内容
	 * @param	output string 输出字符串
	 * @return	void
	 */
	function AppendOutputStream($output) {
		if (is_null($this->_output) || empty($this->_output)) $this->_output = $output;
		else	$this->_output .= $output;
	}
	
	/**
	 * 显示输出
	 * @param	output string 输出内容
	 * @return	void
	 */
	function Display($output=''){
		if ($output == '') $output = & $this->_output;
		echo $output;
	}
}
?>
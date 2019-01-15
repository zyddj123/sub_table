<?php
/**
 * 数据输入类
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * co数据输入类
* 用于获取浏览器请求的输入。如POST, GET, COOKIE等
* 过滤XSS等数据
* ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
class CO_Input{
	//GET数据
	private $__get=null;
	
	//POST数据
	private $__post=null;
	
	//COOKIE数据
	private $__cookie=null;
	
	/**
	 * 构造函数
	 * html转义字符过滤
	 */
	function __construct(){
		$this->__Init();
	}
	
	/**
	 * 初始化
	 * 针对$_GET,$_POST,$_COOKIE中的字符进行过滤
	 * @return	boolean 是否成功
	 */
	private function __Init(){
		//将GET数据依次放入数组中
		foreach($_GET as $key => $val){
			$this->__get[$key]=$val;
		}
		
		//将POST数据依次放入数组中
		foreach($_POST as $key => $val){
			$this->__post[$key]=$val;
		}
		
		//将COOKIE数据依次放入数组中
		foreach($_COOKIE as $key => $val){
			$this->__cookie[$key]=$val;
		}
		
		return true;
	}
	
	/**
	 * 获取$_GET中的值
	 * @param	key string 键值
	 * @param	blnAnti boolean 是否过滤
	 * @return	mixed 不存在返回null,存在返回string
	 */
	public function Get($key=null, $blnAnti=true){
		if($this->__get===null) return null;
		if($key===null){
			$arrRet=array();
			foreach($this->__get as $_key =>$_val){
				$arrRet[self::Anti_XSS($_key)]=self::Anti_XSS($_val);
			}
			return $arrRet;
		}
		if($key=='') return '';
		if(!is_array($this->__get) || !isset($this->__get[$key])) return null;
		return $blnAnti?self::Anti_XSS($this->__get[$key]):$this->__get[$key];
	}
	
	/**
	 * 获取$_POST中的值
	 * @param	key string 键值
	 * @param	blnAnti boolean 是否过滤
	 * @return	mixed 不存在返回null,存在返回string
	 */
	public function Post($key=null, $blnAnti=true){
		if($this->__post===null) return null;
		if($key===null){
			$arrRet=array();
			foreach($this->__post as $_key =>$_val){
				$arrRet[self::Anti_XSS($_key)]=self::Anti_XSS($_val);
			}
			return $arrRet;
		}
		if($key=='') return '';
		if(!is_array($this->__post) || !isset($this->__post[$key])) return null;
		return $blnAnti?self::Anti_XSS($this->__post[$key]):$this->__post[$key];
	}
	
	/**
	 * 获取$_COOKIE中的值
	 * @param	key string 键值
	 * @param	blnAnti boolean 是否过滤
	 * @return	mixed 不存在返回null,存在返回string
	 */
	public function Cookie($key=null, $blnAnti=true){
		if($this->__cookie===null) return null;
		if($key===null){
			$arrRet=array();
			foreach($this->__cookie as $_key =>$_val){
				$arrRet[self::Anti_XSS($_key)]=self::Anti_XSS($_val);
			}
			return $arrRet;
		}
		if($key=='') return '';
		if(!is_array($this->__cookie) || !isset($this->__cookie[$key])) return null;
		return $blnAnti?self::Anti_XSS($this->__cookie[$key]):$this->__cookie[$key];
	}
	
	/**
	 * 过滤提交内容
	 * @param	str string 待过滤的字符
	 * @param	allowedtag string 不需要过滤的字符
	 * @return	string 过滤后的字符
	 */
	static function Anti_XSS($str, $allowedtags = ''){
		$disabledattributes = array(
				'onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 
				'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 
				'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavaible', 'ondatasetchanged', 'ondatasetcomplete', 
				'ondblclick', 'ondeactivate', 'ondrag', 'ondragdrop', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 
				'onerror', 'onerrorupdate', 'onfilterupdate', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 
				'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 
				'onmoveout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 
				'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 
				'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
		if(is_array($str)){
			foreach($str as $key => $val) $str[$key] = self::anti_XSS($val, ALLOWED_HTMLTAGS);
		}else{
			$str= preg_replace(
				'/\s('.implode('|', $disabledattributes).').*?([\s\>])/',
				'\\2',
				preg_replace_callback(
					'/<(.*?)>/i',
					function(){return '';},
					strip_tags($str, $allowedtags)
				)
			);
		}
		return $str;
	}
}
?>
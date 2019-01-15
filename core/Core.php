<?php
/**
 * 核心函数文件
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */

// ------------------------------------------------------------------------

/**
 * 自动加载(__autoload)
 * 自动加载范围是外围根目录的lib,core
 * @param	classname string,需要加载的类名
 * @return
 */
function __autoload($classname) {
	static $lib_file_list = null;
	// 初始化库文件列表
	if ($lib_file_list === null) {
		$cache_file = CACHE_PATH . '/autoload_classes.json';
		if (file_exists ( $cache_file )) {
			// 库文件列表存在,从缓存文件载入
			$lib_file_list = @json_decode ( file_get_contents ( $cache_file ), true );
		}else{
			// 缓存文件不存在,重新生成,并且载入
			$match = '/\/([A-Z]\w*)\.php$/';
			$lib_file_list = __listfile ( $match, LIB_PATH ) + __listfile ( $match, CORE_PATH);
			//排除当前文件(Core.php)
			unset ( $lib_file_list ['Core'] );
			//并且将当前结果缓存到文件中
			file_put_contents ( $cache_file, json_encode ( $lib_file_list ) );
		}
	}
	// 只允许从库文件列表中加载
	if (isset ( $lib_file_list [$classname] )) {
		require_once $lib_file_list [$classname];
	}
}

/**
 * 接收请求url并进行处理
 * 分理处请求controll和function
 * 只识别从index.php接入的访问url
 */
function routes(){
	//获取访问url中?后面的地址
	$path = $_SERVER['QUERY_STRING'];
	//使用正则表达式获取controller和function
	$pattern='/\/(\w*)\/?(\w*)/';
	$matches=array();
	$arrMatches=preg_match($pattern, $path, $matches);
	return $matches;
}

/**
 * 处理未捕获的异常
 * @param exception object 异常对象
 */
function exception_handler($exception){
	$errLog = new CO_Log(APP_LOG_PATH);
	$msg = $exception->getMessage();
	$errLog->write('['.date('Y-m-d H:i:s').'] catch unhandled exception: '.$msg);
	return true;
}
set_exception_handler('exception_handler');

/**
 * 获取数据库操作对象
 * @param	db_name string 数据库连接名称
 * @return	object of db
 */
function GetDB($db_name='default'){
	//引入应用数据库配置
	$db_config = array();
	include APP_CFG_PATH.'/db_config.php';
	if($db_name=='') $db_name='default';
	global $CO_DATABASE;
	if (!isset($CO_DATABASE[$db_name]) || !$CO_DATABASE[$db_name]){
		$db_api_class_file = $db_config[$db_name]['type'].'_api';
		$db_api_class = 'CO_DB_'.$db_config[$db_name]['type'].'_api';
		$db_api_file = realpath(__DIR__).'/db/'.$db_config[$db_name]['type'].'/'.$db_api_class_file.'.php';		
		if(!file_exists($db_api_file)) return false;
		include_once $db_api_file;
		$objDb = new $db_api_class($db_config[$db_name]);
		//选择数据库实例
		$objDb->SelectDb($db_config[$db_name]['db_name']);
		//设置字符集
		$objDb->SetCharset($db_config[$db_name]['charset']);
		$CO_DATABASE[$db_name] = $objDb;
		unset($db_config);
	}
	return $CO_DATABASE[$db_name];
}

/**
 * 404页面
 */
function show404(){
	echo '404';
	die();
}

/**
 * 遍历目录中符合条件的文件列表
 *
 * @param  string $match 正则匹配条件（包括前后的'/'，必须带有至少一组括号以作为结果集索引）
 * @param  string $path  路径（由于匹配时使用绝对路径，如果索引需要相对路径的话可以在$path后加'/'，然后匹配'//'后面的字符，参见@example）
 * @return array         {file_index:file_absolute_path, ...}
 *
 * @example __listfile( '/\/\/(.+\.(?:css|js))$/', '/static/' )    {'dir/a.js':'/static/dir/a.js', 'b.css':'/static/b.css'}
 */
function __listfile($match, $path){
	$list=array();
	foreach(glob($path.'/*')as $item){
		if(is_dir($item)){
			$list+=__listfile($match,$item);
		}elseif(preg_match($match,$item,$matches)){
			$list[$matches[1]]=$item;
		}
	}
	return $list;
}

/**
 * 将一个以下划线分隔的单词字符串更改为骆驼拼写法
 * @param	str string 输入字符串
 * @param	upper_case boolean 大驼峰还是小驼峰
 * @return	string 转化后的字符串
 */
function str_camelize($str, $upper_case = true) {
	$str = strtolower ( $str );
	$arr = explode ( '_', $str );
	$arr = array_map ( "ucfirst", $arr );
	$new_str = implode ( '', $arr );
	return $upper_case ? $new_str : lcfirst ( $new_str );
}
?>
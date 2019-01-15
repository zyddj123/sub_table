<?php
//非法访问 
if (!defined('BASECHECK')){
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
	exit;
}
/**
 * 框架配置
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * 框架配置
 * ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
//错误报告方式
error_reporting(E_ALL ^ E_NOTICE);

//是否将错误信息作为输出的一部分显示到屏幕，或者对用户隐藏而不显示。生产环境建议不开启
ini_set('display_errors','On');

//设置是否将脚本运行的错误信息记录到服务器错误日志或者error_log之中。生产环境建议开启
ini_set('log_errors','Off');

//网站根目录绝对路径(ROOT_PATH)
define('ROOT_PATH', str_replace("\\","/",realpath(dirname(__FILE__).'/../')));

//网站http相对根目录
define('HTTP_ROOT_PATH', str_replace(str_replace('\\', '/', (strrpos($_SERVER['DOCUMENT_ROOT'], '/'))==strlen($_SERVER['DOCUMENT_ROOT'])-1)?substr($_SERVER['DOCUMENT_ROOT'], 0, strlen($_SERVER['DOCUMENT_ROOT'])-1):($_SERVER['DOCUMENT_ROOT']), '', ROOT_PATH));

//系统日志目录
define('LOG_PATH_NAME', 'log');
define('LOG_PATH', ROOT_PATH.'/'.LOG_PATH_NAME);

//核心代码目录
define('CORE_PATH_NAME', 'core');
define('CORE_PATH', ROOT_PATH.'/'.CORE_PATH_NAME);

//lib目录
define('LIB_PATH_NAME', 'lib');
define('LIB_PATH', ROOT_PATH.'/'.LIB_PATH_NAME);

//常量及配置目录
define('CFG_PATH_NAME', 'config');
define('CFG_PATH', ROOT_PATH.'/'.CFG_PATH_NAME);

//缓存目录
define('CACHE_PATH', ROOT_PATH."/cache");

//设置时区
date_default_timezone_set('Asia/Shanghai');

//当前时间戳
define('NOW_TIMESTAMP', time());

//系统默认语言
define('LANG_DEFAULT', 'zh-cn');

//默认视图主题
define('VIEW_DEFAULT_THEMES', 'default');
?>

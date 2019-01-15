<?php
//应用访问入口标记
define('BASECHECK',true);

/**
 * 程序入口文件index
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */
/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * 应用系统程序入口
 * 
 * 引入必要的配置文件
 * ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

/*
 * 引入框架公共配置及常量文件
 */
include realpath(__DIR__.'/../').'/config/global_config.php';

/*
 * 引入应用系统配置及常量文件
 */
include realpath(__DIR__).'/config/system_config.php';

/*
 * 引入框架核心函数文件
 */
include ROOT_PATH.'/core/Core.php';

/*
 * 引入应用系统公共函数文件
 */
include APP_CORE_PATH.'/common.php';

/*
 * 引入应用系统数据库配置文件
 */
include APP_CFG_PATH.'/db_config.php';

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * 定义页面输出对象
 * ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
$out = new Output();

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * 根据输入地址，获取控制器和函数
 * 默认控制器是IndexController
 * 控制器默认函数是run()方法
 * ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
$segments = routes();
$strCtrlor = $segments[1];
$strFunc = $segments[2];
$strCtrlor=empty($strCtrlor)?'index':$strCtrlor;
$strFunc=empty($strFunc)?'run':$strFunc;
unset($segments);

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * 引入控制器文件，并生成控制器对象。
 * 系统运行控制器函数逻辑
 * ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
$controller=str_camelize($strCtrlor).'Controller';
//生成控制器文件路径
$conFile=CONTROLLER_PATH."/{$controller}.php";
if(file_exists($conFile)){
    //控制器基类Controller的方法不能直接调用
    if(is_callable(array('Controller',$strFunc))){
        die("{$strFunc} method is not callable!");
    }
    //引入控制器文件，并生成控制器对象
    include $conFile;
    $objCtrl=new $controller(array(
    		"mod"=>$strCtrlor,
    		"act"=>$strFunc
    		));
    //绑定页面输出对象
    $objCtrl->SetOutput($out);
    if(method_exists($objCtrl, $strFunc)){
    	$ref = new ReflectionMethod($controller, $strFunc);
    	$r = Reflection::getModifierNames($ref->getModifiers());
    	if ($r[0]!='public'){
    		//只可访问public方法
    		unset($r);unset($ref);
    		show404();
    	}
    	//运行指定的控制器方法
    	$objCtrl->$strFunc();
    	//将内容输出
    	$objCtrl->Display();
    }else show404();
}else	show404();
?>

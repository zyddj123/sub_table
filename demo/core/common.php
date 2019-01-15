<?php
/**
 * 应用公共函数文件
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
 * 将包含[]的字符串拆分成数组
 * @param	string string 要拆分的字符串
 * @param	delimiter string 分隔符
 * @return	array 返回数组
 * @example 字符串[a],[b],[c],[d]拆分成数组array(a,b,c,d)
 */
function m_split($string, $delimiter=","){
	$retArray=array();
	if($string=="") return $retArray;
	foreach (explode($delimiter, $string) as $val){
		$val=str_replace('[', '', $val);
		$val=str_replace(']', '', $val);
		array_push($retArray, $val);
	}
	return $retArray;
}

/**
 * 将数组合并成包含[]的字符串
 * @param	array array 要合并的数组
 * @param	glue string 分隔符
 * @return	string 返回字符串
 * @example 数组array(a,b,c,d)合并成字符串[a],[b],[c],[d]
 */
function m_join($array, $glue=","){
	if($array==""|| !is_array($array) || count($array)==0) return "";
	$newArr=array();
	foreach ($array as $val){
		if($val!="") array_push($newArr, '['.$val.']');
	}
	return implode($glue, $newArr);
}

/**
 * 依次创建文件夹
 * @param	dir string 需要创建的文件夹名称
 * @return	boolean 是否成功
 */
function CreateDir($dir){
	if(!file_exists($dir)){
		CreateDir(dirname($dir));
		mkdir($dir,0755);
	}
	return true;
}

/**
 * 依次删除文件夹下的所有文件夹
 * @param	dir string 需要删除的文件夹名称
 * @return	boolean 是否成功
 */
function DelDir($dir){
	//先删除目录下的文件
	if(!is_dir($dir)) return false;
	$dh=opendir($dir);
	while ($file=readdir($dh)){
		//遍历目录下所有文件
		if($file!="." && $file!=".."){
			$fullpath=$dir."/".$file;
			if(!is_dir($fullpath))	unlink($fullpath);
			else	DelDir($fullpath);
		}
	}
	closedir($dh);
	//删除当前文件夹：
	if(rmdir($dir))	return true;
	else	return false;
}
?>

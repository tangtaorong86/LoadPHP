<?php
/**
 * IIS 的$_SERVER兼容处理
 */
namespace Driver\ServerVars;
use Driver\ServerVarsInterface;
class IIS implements ServerVarsInterface{
	
	public function __construct(){}

	public function requestUri(){
		$result = "";
		if(isset($_SERVER['HTTP_X_REWRITE_URL'])){
			$result = urldecode($_SERVER['HTTP_X_REWRITE_URL']);
		}else if(isset($_SERVER['HTTP_X_ORIGINAL_URL'])){
			$result = urldecode($_SERVER['HTTP_X_ORIGINAL_URL']);
		}else if(isset($_SERVER['REQUEST_URI'])){
			$result = $_SERVER['REQUEST_URI'];
		}
		return $result;
	}

	public function realUri(){
		$result  = $_SERVER['SCRIPT_NAME'];
		$trimDir = dirname($_SERVER['SCRIPT_NAME']);
		$trimDir = $trimDir == "/" ? "" : $trimDir;

		if(isset($_SERVER['HTTP_X_REWRITE_URL'])){
			$_SERVER['HTTP_X_REWRITE_URL'] = strtr($_SERVER['HTTP_X_REWRITE_URL'],array($trimDir => "",$result => ""));
			$result .= urldecode($_SERVER['HTTP_X_REWRITE_URL']);
		}else if(isset($_SERVER['HTTP_X_ORIGINAL_URL'])){
			$_SERVER['HTTP_X_ORIGINAL_URL'] = strtr($_SERVER['HTTP_X_ORIGINAL_URL'],array($trimDir => "",$result => ""));
			$result .= urldecode($_SERVER['HTTP_X_ORIGINAL_URL']);
		}
		return $result;
	}
	
}
?>
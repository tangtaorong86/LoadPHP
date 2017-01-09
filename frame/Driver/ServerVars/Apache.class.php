<?php
/**
 * Apache 的$_SERVER兼容处理
 */
namespace Driver\ServerVars;
use Driver\ServerVarsInterface;
class Apache implements ServerVarsInterface{
	
	public function __construct(){}

	public function requestUri(){
		return $_SERVER['REQUEST_URI'];
	}

	public function realUri(){
		
		$result = $_SERVER['SCRIPT_NAME'];
		
		if(isset($_SERVER['ORIG_PATH_INFO'])){
			
			if(strpos($_SERVER['ORIG_PATH_INFO'],$result) === false){
				$result .= $_SERVER['ORIG_PATH_INFO'];
			}else{
				$result = $_SERVER['ORIG_PATH_INFO'];
			}
			
		}else if(isset($_SERVER['PATH_INFO'])){
			
			if(strpos($_SERVER['PATH_INFO'],$result) === false){
				$result .= $_SERVER['PATH_INFO'];
			}else{
				$result = $_SERVER['PATH_INFO'];
			}
			
		}else if(isset($_SERVER['REQUEST_URI'])){
			
			if(strpos($_SERVER['REQUEST_URI'],$result) === false){
				//一级目录
				if(strlen(dirname($result)) == 1){
					$result .= $_SERVER['REQUEST_URI'];
				}else{
					$result .= trim(str_replace(dirname($result),"",$_SERVER['REQUEST_URI']),"/\\");
				}
			}else{
				$result = $_SERVER['REQUEST_URI'];
			}
			
		}
		return $result;
	}
}
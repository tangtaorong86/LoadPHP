<?php
/**
 * Nginx 的$_SERVER兼容处理
 */
namespace Driver\ServerVars;
use Driver\ServerVarsInterface;
class Nginx implements ServerVarsInterface{
	
	public function __construct(){}

	public function requestUri(){
		return $_SERVER['REQUEST_URI'];
	}

	public function realUri(){
		$result = "";
		if(isset($_SERVER['DOCUMENT_URI']) ){
			$result = $_SERVER['DOCUMENT_URI'];
		}elseif( isset($_SERVER['REQUEST_URI'])){
			$result = $_SERVER['SCRIPT_NAME'].$_SERVER['REQUEST_URI'];
		}
		return $result;
	}
	
}
?>
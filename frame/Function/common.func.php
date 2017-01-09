<?php
//namespace Function\common;

/**
 * 特殊字符过滤
 *
 * @param $value
 * @return void 
 */
function request_filter(&$value){
	// TODO 其他安全过滤
	
	// 过滤查询特殊字符
    if(preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i',$value)){
        $value .= ' ';
    }
}

/**
 * 打印字符串
 * @param $str 字符串
 * @return void 
 */
function dump($str){
	var_dump($str);	
}

/**
 * URL重定向
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time=0, $msg=''){
    //多行URL地址支持
    $url = str_replace(array("\n", "\r"), '', $url);
    if(empty($msg))$msg = "系统将在{$time}秒之后自动跳转到{$url}！";
    
    if(!headers_sent()){
        if(0 === $time){
            header('Location: ' . $url);
        }else{
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    }else{
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if($time != 0)$str .= $msg;
        exit($str);
    }
}

/**
 * 递归创建目录
 *
 * @return void
 */
function dmkdir($dir, $mode = 0777, $makeindex = TRUE){
	if(!is_dir($dir)) {
		dmkdir(dirname($dir), $mode, $makeindex);
		@mkdir($dir, $mode);
		if(!empty($makeindex)) {
			@touch($dir.'/index.html'); @chmod($dir.'/index.html', 0777);
		}
	}
	return true;
}

/**
 * 获取客服端ip
 *
 * @return void 
 */
function get_client_ip(){
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
	   $ip = getenv("HTTP_CLIENT_IP");
	else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
	   $ip = getenv("HTTP_X_FORWARDED_FOR");
	else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
	   $ip = getenv("REMOTE_ADDR");
	else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
	   $ip = $_SERVER['REMOTE_ADDR'];
	else
	   $ip = "unknown";
	return($ip);
}


/**
 * 生成随机字符串
 *
 * @return void
 * @author  
 */
 function uniqid_md5() {
	return md5(uniqid(rand(), true));
}
?>
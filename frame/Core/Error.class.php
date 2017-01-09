<?php
/**
 * 异常、错误处理类
 *
 * @package Error
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */
namespace Core;
class Error extends \Exception{
	
	/**
	 * 配置信息
	 *
	 * @var array
	 */
	static $config;
	
    /**
     * 构造函数
     */
   	public function __construct($message="",$code=0) {
   		// 确保所有变量都被正确赋值  
        parent::__construct($message, $code);    
        self::$config = Factory::getConfig("app");
        if(self::$config['ERROR_HANDLE']){
        		// 定义PHP程序执行完成后执行的函数
	    		register_shutdown_function('Core\Error::error_fatal');
	        	// 设置一个用户定义的错误处理函数
		    set_error_handler('Core\Error::error_handle');
	    }
		//自定义异常处理。
		set_exception_handler('Core\Error::exception_handle');
    } 
	
	/**
	 * 显示sql错误
	 *
	 * @return void
	 * @author tangtaorong86
	 */
	public function error_sql($sql,$error,$traceInfo){
		$errorInfo = "[SQL错误：{$error[1]}]SQLState:".$error[0]."@@"."ErrorInfo:".$error[2]."@@SQL:".$sql;
		self::_writeLog($errorInfo);
		$traceTr = self::_showTrace($traceInfo);
		include_once(self::$config['TIPS_TPL_ERROR_SQL']);
        exit();
	}
	
	/**
	 * 显示致命错误
	 *
	 * @return void
	 * @author tangtaorong86
	 */
	static function error_fatal(){
		if ($error = error_get_last()) {
            switch($error['type']){
              case E_ERROR:
              case E_PARSE:
              case E_CORE_ERROR:
              case E_COMPILE_ERROR:
              case E_USER_ERROR:  
                ob_end_clean();
				$e = array();
				//调试模式下输出错误信息
	            if (!is_array($error)) {
	                $trace          = debug_backtrace();
	                $e['message']   = $error;
	                $e['file']      = $trace[0]['file'];
	                $e['line']      = $trace[0]['line'];
	                ob_start();
	                debug_print_backtrace();
	                $e['trace']     = ob_get_clean();
	            }else{
	                $e = $error;
	            }
				//写入日志
				$errorInfo = "[代码错误：{$e['line']}行]文件：{$e['file']}第{$e['line']}行错误提示：{$e['message']}";
				self::_writeLog($errorInfo);
		        if(!self::$config['DEBUG']) {
		            //否则定向到错误页面
		            if (!empty(self::$config['ERROR_URL'])) {
		               redirect(self::$config['ERROR_URL']);
		            } else {
		            		$e = array();
		                $message        = is_array($error) ? $error['message'] : $error;
		                $e['message']   = self::$config['SHOW_ERROR_MSG'] ? $message : self::$config['ERROR_MESSAGE'];
		            }
		        }
				include_once(self::$config['TIPS_TPL_ERROR_HANDLE']);
                break;
            }
        }	
	}
	
	/**
	 * 错误处理方法
	 *
     * @param string $errorMessage 提示信息
     * @param int $errorCode 提示代号
     * @param string $errorFile 出错的文件名
     * @param int $errorLine 出错的行号
	 * @return void
	 */
	static function error_handle($errorCode,$errorMessage,$errorFile,$errorLine){
		//写入日志
		$errorMessage = self::$config['SHOW_ERROR_MSG'] ? $errorMessage : self::$config['ERROR_MESSAGE'];
		switch ($errorCode) {
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
			    ob_end_clean();
				if(self::$config['ERROR_URL']){
					redirect(self::$config['ERROR_URL']);
				}else{
					//输出错误信息
					self::show_error($errorCode,$errorMessage,$errorFile,$errorLine);			
				}
			    break;
			default:
			   	self::show_error($errorCode,$errorMessage,$errorFile,$errorLine);	
			break;
      	}
		
		
		
	}
	
	/**
	 * 异常处理方法
	 *
	 * @param $e 异常信息（包含Trace信息）
	 * @return void
	 */
	static function exception_handle($e){
		// 发送404信息
        header('HTTP/1.1 404 Not Found');
        header('Status:404 Not Found');
		$traceInfo = $e->getTrace();
		self::show_error($e->code,$e->message,$e->file,$e->line,$traceInfo);
	}

	/**
	 * 显示错误
	 *
     * @param string $errorMessage 提示信息
     * @param int $errorCode 提示代号
     * @param string $errorFile 出错的文件名
     * @param int $errorLine 出错的行号
     * @param array $traceInfo trace信息
	 * @return string 错误信息输出
	 */		
	static function show_error($errorCode,$errorMessage,$errorFile,$errorLine,$traceInfo=NULL){
 		$lever = array(	
 			1=>'致命错误(E_ERROR)',
			2 =>'警告(E_WARNING)',
			4 =>'语法解析错误(E_PARSE)',  
			8 =>'提示(E_NOTICE)',  
			16 =>'E_CORE_ERROR',  
			32 =>'E_CORE_WARNING',  
			64 =>'编译错误(E_COMPILE_ERROR)', 
			128 =>'编译警告(E_COMPILE_WARNING)',  
			256 =>'致命错误(E_USER_ERROR)',  
			512 =>'警告(E_USER_WARNING)', 
			1024 =>'提示(E_USER_NOTICE)',  
			2047 =>'E_ALL', 
			2048 =>'E_STRICT'
		);
		$errorMessage = strip_tags($errorMessage);
		$errorCode = isset($lever[$errorCode]) ? $lever[$errorCode] : $errorCode;
		$traceTr = self::_showTrace($traceInfo);
		//记录错误
		$errorInfo = "[错误代码：{$errorCode}]{$errorMessage}";
		self::_writeLog($errorInfo);
		include_once(self::$config['TIPS_TPL_ERROR_EXCEPTION']);
		exit();
	}

	/**
	 * 组装trace表格
	 *
	 * @return void
	 * @author  
	 */
	protected static function _showTrace($traceInfo) {
		$traceTr = "";
		if(!empty($traceInfo) && self::$config['SHOW_TRACE_MSG'] && self::$config['DEBUG']){
			$traceTr = '<div class="info">
			<p><strong>Trace Info</strong></p>
			<table cellpadding="5" cellspacing="1" width="100%" class="table">
				<tr class="bg2">
					<td>No.</td>
					<td>File</td>
					<td>Line</td>
					<td>Function</td>
				</tr>';
			foreach($traceInfo as $k=>$v){
				$num = $k+1;
				$class = array_key_exists('class',$v) ? $v['class'].$v['type'] : "";
				$traceTr .= "<tr class='bg1'><td>{$num}</td><td>{$v['file']}</td><td>{$v['line']}</td><td>{$class}{$v['function']}()</td></tr>";
			}
			$traceTr .= "</table></div>";
		}
		return $traceTr;
	}
	
	/**
	 * 写入日志
	 *
	 * @return void
	 * @author  
	 */
	protected static function _writeLog($info) {
		//写入日志
		if(self::$config['LOG_ON']){
			$driver = self::$config['LOG_DRIVER'];
			$log = Factory::getDriver($driver,"Log")->write($info);
		}		
	}

}
?>
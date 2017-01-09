<?php
/**
 * 文本日志处理驱动
 *
 * @package fileLog
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */
/**
 * @class IFileLog
 * @brief 文本格式日志处理类
 */
namespace Driver\Log;
use Driver\LogInterface;
class File implements LogInterface
{
	/**
	 * 日志文件路径
	 *
	 * @var string
	 */
	private $path;
	
	/**
	 * 配置信息
	 *
	 * @var array
	 */
	static public $config = array();
	
	/**
	 * 日志文件大小
	 *
	 * @var string
	 */
	private $size = 2097152;

	/**
	 * 文件日志类的构造函数
	 */
	public function __construct(){
		self::$config = \Core\Factory::getConfig("app");
		$this->path = self::$config['LOG_PATH'];
	}

	/**
	 * 写入日志
	 * @param  string $logs 记录日志内容
	 * @return bool   操作结果
	 */
	public function write($logs = ""){
		if(!$this->path){
			throw new IException('日志目录未定义');
		}
		if(!file_exists($this->path)){
			if(!dmkdir($this->path,0755,TRUE)){
				throw new IException('日志目录创建失败！请检查'.$this->path.'目录的读写状态！');
			}			
		}
		
		$time = date('Y-m-d H:i:s');
		$ip = get_client_ip();
		$log_file = $this->path.date("Y-m-d")."-".md5($this->path).".log";
        //检测日志文件大小，超过配置大小则备份日志文件重新生成
        if(is_file($log_file) && floor($this->size) <= filesize($log_file) ){
            rename($log_file,dirname($log_file).'/'.basename($log_file,".log").'-'.time().".log");
        }		
		//写入文件，记录错误信息
	   	return @error_log("{$time} | {$ip} | {$_SERVER['PHP_SELF']} |{$logs}\r\n",3,$log_file);
	}
}
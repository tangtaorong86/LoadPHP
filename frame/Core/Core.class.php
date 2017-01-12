<?php
/**
 * 核心类
 *
 * @package Core
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */

namespace Core;
class Core{
	/**
	 * 配置
	 *
	 * @var array
	 */
	static public $config;
	
	/**
	 * 单例对象
	 *
	 * @var string
	 */
	protected static $instance;
	
	//构造方法
	protected function __construct(){
		//注册类的自动载入
		spl_autoload_register('self::autoload');
		
		//定制异常、错误处理
		new Error();
		
		//加载app配置，设置时区，设置报错模式
		self::$config = Factory::getConfig("app");
		
		date_default_timezone_set(self::$config['DEFAULT_TIMEZONE']);
		if(self::$config['DEBUG']){
			ini_set("display_errors", 1);
			error_reporting( E_ALL ^ E_NOTICE );
		} else {
			ini_set("display_errors", 0);
			error_reporting(0);
		}
		
		//定义SERVER常量
		define('__SYS_HOST__',self::getHost());//主机
		define('__SYS_SCRIPT__',self::getIndexFile());//当前入口脚本
		define('__SYS_ENTRY__',self::getEntryUrl());//当前入口url
		define('__SYS_DIR__',self::getSysDir());//当前项目目录（不包含index.php）
		
	}
	
	/**
	 * 获取对象
	 *
	 * @var string
	 */
    static function getInstance(){
        if(empty(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
	
	/**
	 * 通过路由获取路径开始走起
	 *
	 * @return void
	 */
	public function run(){
		//通过路由调取uri参数
		$Route = new Route();
		list($module,$controller,$action) = $Route::parseUrl();
		
		//控制器后缀
		$controler_seffix = self::$config['CONTROLER_SUFFIX'];
		//所有分组名称
		$module_list = explode(",",self::$config['DEFAULT_MODULE_LIST']);
		//控制器命名空间
		$namespace_base = APP_PATH."\\".$module."\\".self::$config['CONTROLER_PATH'];
		//具体控制器类
		$controller_class = $namespace_base."\\".$controller.$controler_seffix;
		//空控制器类
		$empty_controller_class = $namespace_base."\\".self::$config['CONTROLER_EMPTY'].$controler_seffix;
		
		//模块分组不存在，抛出错误
		if(!in_array($module,$module_list)){
			throw new \Exception("分组{$module}不存在", 404);
		}
		
		//控制器不存在调空控制器，空也不存在抛出错误
		if(!class_exists($controller_class)){
			if(!class_exists($empty_controller_class)){
				throw new \Exception("控制器{$controller}不存在", 404);
			}else{
				$controller = self::$config['CONTROLER_EMPTY'];
				$controller_class = $empty_controller_class;
			}
		}
		$controller_object = new $controller_class;
		
		//方法不存在调空方法，空也不存在抛出错误
		if(!method_exists($controller_object, $action)){
			if(!method_exists($controller_object,self::$config['ACTION_EMPTY'])){
				throw new \Exception("{$controller}控制下不存在{$action}()方法", 404);
			}else{
				$action = self::$config['ACTION_EMPTY'];
			}
		}
		
		//后期扩展actionBefore		
		$controller_object->$action();
		//后期扩展actionAfter
	}
		
	//惰性收集需要载入的类
	static function autoload($class){
		if(strpos($class,'\\') !== FALSE){
			$base_name = strstr($class, '\\', TRUE);
			$frame_dir = explode(",",FRAME_DIR);
			$namespace_base = in_array($base_name,$frame_dir) ? FRAME_PATH : BASE_PATH;
			$class_file = $namespace_base.'/'.str_replace('\\', '/', $class).".".CLASS_EXT;
			if(file_exists($class_file)){
				require $class_file;
			}
		}
	}
	
	/**
	 * @brief 获取网站根路径
	 * @return String $baseUrl  网站根路径
	 */
	public static function getHost(){
		if(isset($_SERVER['SERVER_PROTOCOL'])){
			$schemeArray = explode("/",$_SERVER['SERVER_PROTOCOL']);
			$scheme      = trim($schemeArray[0]);
		}
		$scheme  = $scheme ? $scheme : "http";
		$host	 = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : 
		(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
		$baseUrl = strtolower($scheme).'://'.$host;
		return $baseUrl;
	}
	
	/**
	 * 返回入口文件URl地址(目前兼容Apache、IIS、Nginx需要在配置文件里做配置)
	 * @return string 返回入口文件URl地址
	 */
	public static function getEntryUrl(){
		$obj = Factory::getDriver(self::$config['SERVER_TYPE'],"ServerVars");
		$url = $obj->realUri();
		return self::getHost().$url;
	}
	
	/**
	 * 返回入口文件URl地址
	 * @return string 返回入口文件URl地址
	 */
	public static function getSysDir(){
		return self::getHost().self::getScriptDir();
	}
	
	
	/**
	 * 获取当前脚本所在文件夹
	 * @return 脚本所在文件夹
	 */
	public static function getScriptDir(){
		$return = strtr(dirname($_SERVER['SCRIPT_NAME']),"\\","/");
		return $return == '/' ? '/' : $return.'/';
	}

	/**
	 * 获取入口文件名
	 */
	public static function getIndexFile(){
		return isset($_SERVER['SCRIPT_NAME']) ? basename($_SERVER['SCRIPT_NAME']) : 'index.php';
	}
	
}

?>
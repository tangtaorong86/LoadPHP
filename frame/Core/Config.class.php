<?php
/**
 * 配置类，继承了ArrayAccess接口，使配置对象可以像数组一样操作
 *
 * @package Config
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */
namespace Core;
class Config implements \ArrayAccess{
	/**
	 * 路径
	 *
	 * @var string
	 */
    protected $path;
    
    /**
     * 配置
     *
     * @var string
     */
    protected $configs = array();

	/**
	 * $path代表配置文件所在目录
	 *
	 * @param $path 路径
	 * @return  
	 */
    function __construct($path){
        $this->path = $path;
    }
	
	/**
	 * 获取配置
	 *
	 * @param $key 
	 * @return  
	 */
    function offsetGet($key){
    		$config_default = array();
    		$config_diy = array();
        if (empty($this->configs[$key]))
        {
            $config_file = $this->path.'/'.$key.'.php';
            if(file_exists($config_file)){
            		$config_diy = require $config_file;
            }
			$config_default = self::_defaultConfig($key);
            $this->configs[$key] = array_merge($config_default,$config_diy);
        }
        return $this->configs[$key];
    }
	
	/**
	 * 设置
	 *
	 * @param $key
	 * @param $value
	 * @return 
	 */
    function offsetSet($key, $value){
        throw new \Exception("cannot write config file.");
    }
	
	/**
	 * 检测
	 *
	 * @param $key
	 * @param $value
	 * @return 
	 */
    function offsetExists($key){
        return isset($this->configs[$key]);
    }
	
	/**
	 * 销毁
	 *
	 * @param $key
	 * @return 
	 */
    function offsetUnset($key){
        unset($this->configs[$key]);
    }
    
	/**
	 * 默认配置
	 *
	 * @param $key
	 * @return 
	 */
    protected static function _defaultConfig($key){
		$config =  array(		
			//应用配置
			'APP' => array(
				//设置server类型，url解析必须要用到此配置
				'SERVER_TYPE' => 'Apache',
				
				//日志和错误调试配置
				'DEBUG' => TRUE,	//是否开启调试模式
				'LOG_ON' => TRUE,//是否开启出错信息保存到文件
				'LOG_DRIVER' => "File",//使用File（文本方式记录日志）
				'LOG_PATH' => DATA_PATH.'/log/',//出错信息存放的目录，出错信息以天为单位存放
				
				'VAR_MODULE'            =>  'm',      // 默认模块获取变量
			    'VAR_CONTROLLER'        =>  'c',      // 默认控制器获取变量
			    'VAR_ACTION'            =>  'a',      // 默认操作获取变量
			    'DEFAULT_MODULE_LIST'   =>  'home',   // 分组数据
			    'DEFAULT_MODULE'        =>  'home',   // 默认模块
			    'DEFAULT_MODULE_GLOBAL' =>  'global', // 默认公共模块
			    'DEFAULT_TIMEZONE'      => 'PRC',     //默认时区
			    
				 /* 错误设置 */
			    'ERROR_MESSAGE'         =>  'Sorry！页面错误！请稍后再试...',//错误显示信息,非调试模式有效
			    'SHOW_ERROR_MSG'        =>  TRUE,    // 显示错误信息,调试模式有效
			    'SHOW_TRACE_MSG'        =>  TRUE,  //是否显示trace信息,调试模式有效
				'ERROR_URL' => '',//出错信息重定向页面，为空采用默认的出错页面，一般不需要修改
				'ERROR_HANDLE'=>TRUE,//是否启动框架内置的错误处理，如果开启了xdebug，建议设置为false
				
				'TIPS_TPL_SUCCESS'      => FRAME_PATH."/Static/Tpl/success.php", //操作成功
			    'TIPS_TPL_ERROR'         => FRAME_PATH."/Static/Tpl/error.php", //操作失败
			    'TIPS_TPL_ERROR_EXCEPTION'    => FRAME_PATH."/Static/Tpl/error_exception.php", //异常提示
			    'TIPS_TPL_ERROR_HANDLE'    => FRAME_PATH."/Static/Tpl/error_handle.php", //错误提示
			    'TIPS_TPL_ERROR_SQL'          => FRAME_PATH."/Static/Tpl/error_sql.php", //SQL错误
				
				//控制器配置
				'CONTROLER_PATH' => 'controller',//控制器存放目录，一般不需要修改
				'CONTROLER_SUFFIX' => 'Controller',//控制器后缀，如：IndexControler.class.php
				'CONTROLER_DEFAULT' => 'Index',//默认控制器，一般不需要修改
				'CONTROLER_EMPTY' => 'Empty',//空控制器	，一般不需要修改	
		
				//操作配置
				'ACTION_DEFAULT' => 'index',//默认操作，一般不需要修改
				'ACTION_EMPTY' => '_empty',//空操作，一般不需要修改
//	
				//模型配置
				'MODEL_PATH' => 'model',//模型存放目录，一般不需要修改
				'MODEL_SUFFIX' => 'Model',//模型后缀，一般不需要修改
				
				'VAR_AJAX_SUBMIT'       =>  'ajax',  // 默认的AJAX提交变量
    				'VAR_JSONP_HANDLER'     =>  'callback',
//								
			),
			'ROUTE' => array(
				//网址配置
				'URL_REWRITE_POWER' => FALSE,//是否开启重写，true开启重写,false关闭重写
//				'URL_MODULE_DEPR' => '/',//模块分隔符，一般不需要修改
//				'URL_ACTION_DEPR' => '-',//操作分隔符，一般不需要修改
//				'URL_PARAM_DEPR' => '-',//参数分隔符，一般不需要修改
//				'URL_HTML_SUFFIX' => '.html',//伪静态后缀设置，例如 .html ，一般不需要修改
				'ROUTE_POWER' => FALSE,
				'ROUTE_RULE' => array()
			),
			'CACHE' => array(
				'DATA_CACHE_TIME'       =>  0,      // 数据缓存有效期 0表示永久缓存
				'DATA_CACHE_TYPE'       =>  'File',  // 数据缓存类型,支持:File
				'DATA_CACHE_PATH'       =>  DATA_PATH.'/cache/',// 缓存路径设置 (仅对File方式缓存有效)
				'DATA_PATH_LEVEL'       =>  1,        // 子目录缓存级别
			),
			//数据库配置
			'DB'  => array(								
				'DB_TYPE' => 'mysqli',//数据库类型，一般不需要修改
				'DB_HOST' => 'localhost',//数据库主机，一般不需要修改
				'DB_USER' => 'root',//数据库用户名
				'DB_PWD' => 'root',//数据库密码
				'DB_PORT' => 3306,//数据库端口，mysql默认是3306，一般不需要修改
				'DB_NAME' => 'php',//数据库名
				'DB_CHARSET' => 'utf8',//数据库编码，一般不需要修改
				'DB_PREFIX' => 'pre_',//数据库前缀
				'DB_SLAVE'  => array(
					array(
						'DB_TYPE' => 'mysqli',//数据库类型，一般不需要修改
						'DB_HOST' => 'localhost',//数据库主机，一般不需要修改
						'DB_USER' => 'root',//数据库用户名
						'DB_PWD' => 'root',//数据库密码
						'DB_PORT' => 3306,//数据库端口，mysql默认是3306，一般不需要修改
						'DB_NAME' => 'php',//数据库名
						'DB_CHARSET' => 'utf8',//数据库编码，一般不需要修改
						'DB_PREFIX' => 'pre_',//数据库前缀
					),
					array(
						'DB_TYPE' => 'mysqli',//数据库类型，一般不需要修改
						'DB_HOST' => 'localhost',//数据库主机，一般不需要修改
						'DB_USER' => 'root',//数据库用户名
						'DB_PWD' => 'root',//数据库密码
						'DB_PORT' => 3306,//数据库端口，mysql默认是3306，一般不需要修改
						'DB_NAME' => 'php',//数据库名
						'DB_CHARSET' => 'utf8',//数据库编码，一般不需要修改
						'DB_PREFIX' => 'pre_',//数据库前缀
					),
				),
			),
			
			//模板配置
			'TPL' => array(
				'TPL_TEMPLATE_PATH'=>'/view/', //模板目录，一般不需要修改
				'TPL_FILE_DEPR' =>  '-', //模板文件CONTROLLER_NAME与ACTION_NAME之间的分割符
				'TPL_THEME_MODULE' => array(
//					'home'=>'default', //模块名称=>主题名称
				),
				'TPL_DRIVER_ON' => TRUE, //是否开启模板引擎，开启后下面配置才生效,为开启后缀默认为php，并且不生成模板缓存
				
				'TPL_DRIVER' => 'Normal',//默认模板引擎驱动
				'TPL_LEFT_DELIMITER' => '<!--{', //左边边界
				'TPL_RIGHT_DELIMITER' =>'}-->', //右边边界
				'TPL_CACHE_ON'=> FALSE, //是否开启模板缓存，true开启,false不开启
				'TPL_CACHE_PATH'=> DATA_PATH.'/tpl_cache/', //模板缓存目录，一般不需要修改
				'TPL_CACHE_SUFFIX'=>'.html', //模板缓存后缀,一般不需要修改
			),
			
		);  		
		$key = strtoupper($key);
		if(array_key_exists($key,$config)){
			return $config[$key];
		}else{
			return array();
		}
    }
}
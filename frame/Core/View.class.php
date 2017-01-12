<?php
/**
 * 视图类
 *
 * @package View
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */

namespace Core;
class View{
	
	/**
	 * 模板配置
	 *
	 * @var string
	 */
    protected $tpl_config;
    
    /**
     * 模块名称
     *
     * @var string
     */
    protected $module_name;
    
    /**
     * 控制器名称
     *
     * @var string
     */
    protected $controller_name;
    
    /**
     * 方法名称
     *
     * @var string
     */
    protected $action_name;
    
    /**
     * 模板目录
     *
     * @var string
     */
    protected $template_dir;
    
    /**
     * 主题名称
     *
     * @var string
     */
    protected $theme;
    
    /**
     * 模板引擎驱动
     *
     * @var string
     */
    protected $driver;

	/**
	 * 构造方法
	 *
	 * @return void
	 * @author  
	 */
    function __construct(){
    		
    	$this->tpl_config = config("tpl");
    	$this->driver = Factory::getDriver($this->tpl_config['TPL_DRIVER'],"Tpl");
    		
    	//常量赋值
    	$this->module_name = MODULE_NAME;
        $this->controller_name = CONTROLLER_NAME;
        $this->action_name = ACTION_NAME;
        
        //找到模板
        if(array_key_exists(MODULE_NAME,$this->tpl_config['TPL_THEME_MODULE'])){
        		$this->theme = $this->tpl_config['TPL_THEME_MODULE'][MODULE_NAME];
        		$dir = $this->tpl_config['TPL_TEMPLATE_PATH'].$this->theme."/";
        }else{
        		$dir = $this->tpl_config['TPL_TEMPLATE_PATH'];
        }
        $this->template_dir = APP_PATH."/".MODULE_NAME.$dir;
    }

	/**
	 * 引入变量
	 *
	 * @param string $key 模板变量
	 * @param string $value 脚本变量
	 * @return void
	 * @author  
	 */
    public function assign($key, $value){
    		if(!$this->tpl_config['TPL_DRIVER_ON']){
        		$this->data[$key] = $value;
      	}else{
      		$this->driver->assign($key,$value);
      	}
    }

	/**
	 * 渲染模板
	 *
	 * @param string $file 模板文件名
	 * @return void
	 * @author  
	 */
    public function display($file = ''){
        $path = $this->_check_tpl($file);
    		if(!$this->tpl_config['TPL_DRIVER_ON']){
    			if(!empty($this->data))extract($this->data);
    			include $path;
    		}else{
			$this->driver->display($path);    			
    		}
    }
 
 	/**
 	 * 获取模板内容
 	 *
 	 * @param string $file 模板文件名
 	 * @return void
 	 */
 	public function fetch($file = ''){
 		$path = $this->_check_tpl($file);
    		if(!$this->tpl_config['TPL_DRIVER_ON']){
    			if(!empty($this->data))extract($this->data);
    			return file_get_contents($path);
    		}else{
			return $this->driver->fetch($path);    			
    		}
 	}

 	/**
 	 * 检查并返回带路径的模板文件名
 	 *
 	 * @param string $file 模板文件名
 	 * @return void
 	 */  
    private function _check_tpl($file = ''){
		if (empty($file)){
            $file = $this->controller_name.$this->tpl_config['TPL_FILE_DEPR'].$this->action_name.$this->tpl_config['TPL_CACHE_SUFFIX'];
        }
        $path = $this->template_dir.$file;
        if(!file_exists($path)){
	        throw new \Exception('模板文件：'.$path."不存在!",404);
	    }  
	    return $path;  		
    }
    
}
?>
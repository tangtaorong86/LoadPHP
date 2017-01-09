<?php
/**
 * 工厂类（避免相关操作重复实例化，实现多处使用的方法解耦）
 *
 * @package Factory
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */

namespace Core;
class Factory{
		
	/**
	 * 获取模型
	 * @param $model 模型名称
	 * @return void 
	 */
	static function getModel($model) {
		$class_name = config("APP.MODEL_PATH").'\\'.$model.config("APP.MODEL_SUFFIX");
		$model_namespace = APP_PATH.'\\'.MODULE_NAME.'\\';
		$global_namespace = APP_PATH.'\\'.config("APP.DEFAULT_MODULE_GLOBAL").'\\';
		$default_class = $model_namespace.$class_name;
		$global_class = $global_namespace.$class_name;
		//判断应用目录下是否有该模型类，没有的话从global目录找这个模型类
		$class = class_exists($default_class) ? $default_class : $global_class;
		if(class_exists($class)){
			$key = 'model_'.$model;
	        $model = Register::get($key);
	        if (!$model) {
	            $model = new $class($model);
	            Register::set($key, $model);
	        }
	        return $model;			
		}else{
			throw new \Exception("不存在的模型".$model);
		}
	}

    /**
     * 获取配置，避免多处实例化
     * @param $key
     * @return config
     */
    static function getConfig($cate){
        $key = 'config_'.$cate;
        $config = Register::get($key);
        if (!$config) {
            $config = new Config(APP_CONFIG);
        		$config = $config[$cate];
            Register::set($key, $config);
        }
        return $config;
    }
    
    
	 /**
     * 实例化驱动类
     *
     * @param $driver 驱动类型
	 * @param $dirver_type 驱动模式 如Mysql
	 * @param $singleton 是否单例模式 如Db
     * @return void
     */
    static function getDriver($driver,$driver_type,$singleton=0){
        $key = 'app_'.$driver_type.'_driver_'.$driver;
        $driver_res = Register::get($key);
        if (!$driver_res) {
        		$driver_class = "Driver\\".$driver_type."\\".ucwords($driver);
			if(!class_exists($driver_class)){
				throw new \Exception("{$driver}驱动不存在！", 404);
			}
            $driver_res = $singleton ? $driver_class::getIntance() : new $driver_class;
            Register::set($key, $driver_res);
        }
        return $driver_res;
    }
	
	/**
	 * 显示sql错误
	 *
	 * @return void
	 * @author  
	 */
	static function showSqlError($sql,$errorArr){
		$trace = debug_backtrace();
		$errorObj = new Error();
		$errorObj->error_sql($sql,$errorArr,$trace);
	}
	
}
<?php
/**
 * 全局注册器:把对象注册到全局树上（注册模式），比设置全局变量更加安全，不可覆盖
 *
 * @package 
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */
namespace Core;
class Register{
	
	/**
	 * 存放共享数据
	 *
	 * @var array
	 */
    protected static $objects;
    
    static function set($alias, $object){
        self::$objects[$alias] = $object;
    }

    static function get($key){
        if (!isset(self::$objects[$key])){
            return false;
        }
        return self::$objects[$key];
    }

    function _unset($alias){
        unset(self::$objects[$alias]);
    }
    
}
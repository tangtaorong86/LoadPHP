<?php
/**
 * LoadPHP框架启动脚本
 *
 * @package run.php
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */

if (version_compare(PHP_VERSION, '5.3.0','<')) {
	header("Content-Type: text/html; charset=UTF-8");
    die('抱歉！PHP环境不能低于5.3.0！');
}
//系统常量
define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
 define('IS_AJAX', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')));
define('IS_GET',REQUEST_METHOD =='GET' ? true : false);
define('IS_POST',REQUEST_METHOD =='POST' ? true : false);
define('IS_PUT',REQUEST_METHOD =='PUT' ? true : false);
define('IS_DELETE',REQUEST_METHOD =='DELETE' ? true : false);

//全局常量
define('CLASS_EXT',"class.php");

//框架常量
define('FRAME_VER','0.1.2016.0920');//框架版本号,后两段表示发布日期
define('FRAME_PATH',dirname(__FILE__));//当前文件所在的目录
define('FRAME_DIR','Core,Driver,Library'); //定义框架下目录(核心类，驱动，扩展类)
define('FRAME_FUNCTION_DIR',FRAME_PATH.'/Function');//框架函数目录

//应用常量
if(!defined('BASE_PATH'))define('BASE_PATH', realpath('./'));//根目录
if(!defined('APP_PATH'))define('APP_PATH','app');//应用目录配置，方便项目分组开发
if(!defined('APP_CONFIG'))define('APP_CONFIG', BASE_PATH .'/config');//配置文件目录
if(!defined('DATA_PATH'))define('DATA_PATH', BASE_PATH .'/data');//数据目录（存放日志、缓存文件等）

//加载函数
include FRAME_FUNCTION_DIR."/common.func.php";
include FRAME_FUNCTION_DIR."/core.func.php";

//开启session
if(isset($_COOKIE[session_name()]) && $_COOKIE[session_name()])session_id($_COOKIE[session_name()]);
if(!isset($_SESSION))session_start();

// RGP过滤
array_walk_recursive($_GET,'request_filter');
array_walk_recursive($_POST,	'request_filter');
array_walk_recursive($_REQUEST,'request_filter');

include FRAME_PATH.'/Core/Core.class.php';
Core\Core::getInstance()->run();


?>
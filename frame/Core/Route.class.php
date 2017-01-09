<?php
/**
 * 路由解析类（待完善）
 *
 * @package Route
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */

namespace Core;
class Route{
	/**
	 * 模块名称
	 *
	 * @var string
	 */
	static public $m_name;
	
	/**
	 * 控制器名称
	 *
	 * @var string
	 */
	static public $c_name;
	
	/**
	 * 方法名称
	 *
	 * @var string
	 */
	static public $a_name;

	/**
	 * app配置
	 *
	 * @var array
	 */
	static protected $app_config = array();
	
	/**
	 * 路由配置
	 *
	 * @var array
	 */
	static protected $route_config = array();
	
	/**
	 * 路由正则
	 *
	 * @var array
	 */
	static protected $route_rule = array();
	
	
	/**
	 * 构造函数(后期准备废除该构造函数)
	 *
	 * @return void
	 * @author  
	 */
	public function __construct(){
		//获取配置
		self::$app_config = Factory::getConfig("app");
		self::$route_config = Factory::getConfig("route");
		
		//设置属性值
		self::$route_rule = self::$route_config['ROUTE_RULE'];
		self::$m_name = self::$app_config['VAR_MODULE'];
    		self::$c_name = self::$app_config['VAR_CONTROLLER'];
    		self::$a_name = self::$app_config['VAR_ACTION'];
	}
	
	/**
	 * 路由编译（queryUrl编译也算是路由编译的一种）
	 *
	 * @param $param array("m"=>"home","c"=>"site","a"=>"read","id"=>22);
	 * @return void
	 * @author tangtaorong86
	 */
	public static function makeUrl($param){
		//先判断当前url_type
		if(self::$route_config['URL_TYPE'] == "url"){
			
			$url = self::encodeQueryUrl($param);
			
		}elseif(self::$route_config['URL_TYPE'] == "pathinfo"){
			
			$url = self::encodePathinfoUrl($param);
			
		}elseif(self::$route_config['URL_TYPE'] == "route"){
			$url = self::encodeRouteUrl($param);
			if(!$url){
				$url = self::encodePathinfoUrl($param);
			}
		}else{
			$url = self::encodeQueryUrl($param);
		}
		
		return __SYS_SCRIPT__.$url;
	}
	
    /**
     * 路由解析（queryUrl解析也算是路由解析的一种）
     *
     * @return void
     * @author tangtaorong86
     */
    public static function parseUrl(){

		$obj = Factory::getDriver(self::$app_config['SERVER_TYPE'],"ServerVars");
		$url = $obj->realUri();
		if(self::$route_config['URL_TYPE'] != "url"){
			self::_reSetGet($url);
		}

    		$module_name = empty($_GET[self::$m_name]) ? self::$app_config['DEFAULT_MODULE'] : $_GET[self::$m_name];
    		$controler_name = empty($_GET[self::$c_name]) ? self::$app_config['CONTROLER_DEFAULT'] : $_GET[self::$c_name];
    		$action_name = empty($_GET[self::$a_name]) ? self::$app_config['ACTION_DEFAULT'] : $_GET[self::$a_name];
    		
    		$controler_name = ucfirst(strtolower($controler_name));//控制器类名首字母大写
    		$action_name = strtolower($action_name); //方法名称全部小写
    		
		if(!(preg_match('/^[A-Za-z](\/|\w)*$/',$controler_name))){
			throw new \Exception("控制器 '{$controller}' 名称不合理", 404);
		}
		
		if(!(preg_match('/^[A-Za-z](\/|\w)*$/',$action_name))){
			throw new \Exception("方法 '{$action_name}' 名称不合理", 404);
		}
    		
    		define('MODULE_NAME',$module_name);
    		define('CONTROLLER_NAME',$controler_name);
    		define('ACTION_NAME',$action_name);
		return array($module_name,$controler_name,$action_name);
    }

    
	/**
	 * queryUrl解析
	 * 解析最常见的url如index.php?m=home&c=index&a=content&id=1
	 *
	 * @return void
	 */
	public static function decodeQueryUrl($url){
		if(!is_array($url)){
			$url = parse_url($url);
		}
		$query = isset($url['query']) ? explode("&",$url['query']) : array();
		$re = array();
		foreach($query as $value){
			$tmp = explode("=",$value);
			if(count($tmp) == 2){
				$re[$tmp[0]] = $tmp[1];
			}
		}
		$re = self::_sortUrlArray($re);
		isset($url['fragment']) && ($re[self::$route_config['ROUTE_ANCHOR']] = $url['fragment'] );
		return $re;
	}
	
	/**
	 * queryUrl编译
	 * 把数组编译为常规的url如：index.php?m=home&c=index&a=content&id=1
	 * @param $arr array("m"=>"home","c"=>"index","a"=>"test","name"=>"fasdf");
	 * @return string
	 */
	public static function encodeQueryUrl($arr){
		$query = http_build_query($arr);
		return "?".$query;
	}
	
	/**
	 * 路由规则解析
	 * @param $url string 要解析的url地址
	 * @return array
	 */
	private static function decodeRouteUrl($url){
		$url = trim($url,'/');
		$urlArray  = array();//url的数组形式
		$routeList = self::_getLeverRouteRule();
		
		if(!$routeList)return $urlArray;
		foreach($routeList as $level => $regArray){
			foreach($regArray as $regPattern => $value){
				//解析执行规则的url地址
				$exeUrlArray = explode('/',$value);
				
				//判断当前url是否符合某条路由规则,并且提取url参数
				$regPatternReplace = preg_replace("%<\w+?:(.*?)>%","($1)",$regPattern);
				if(strpos($regPatternReplace,'%') !== false){
					$regPatternReplace = str_replace('%','\%',$regPatternReplace);
				}
				if(preg_match("%$regPatternReplace%",$url,$matchValue)){
					//是否完全匹配整个完整url
					$matchAll = array_shift($matchValue);
					if($matchAll != $url){
						continue;
					}
					//如果url存在动态参数，则获取到$urlArray
					if($matchValue){
						preg_match_all("%<\w+?:.*?>%",$regPattern,$matchReg);
						foreach($matchReg[0] as $key => $val){
							$val                     = trim($val,'<>');
							$tempArray               = explode(':',$val,3);
							$urlArray[$tempArray[0]] = isset($matchValue[$key]) ? $matchValue[$key] : '';
						}

		
						//检测controller和action的有效性
						if(
							(isset($urlArray[self::$m_name]) && !preg_match("%^\w+$%",$urlArray[self::$m_name])) || 
							(isset($urlArray[self::$c_name]) && !preg_match("%^\w+$%",$urlArray[self::$c_name])) || 
							(isset($urlArray[self::$a_name]) && !preg_match("%^\w+$%",$urlArray[self::$a_name]))
						){
							$urlArray  = array();
							continue;
						}

						//对执行规则中的模糊变量进行赋值
						foreach($exeUrlArray as $key => $val){
							$paramName = trim($val,'<>');
							if( ($val != $paramName) && isset($urlArray[$paramName])){
								$exeUrlArray[$key] = $urlArray[$paramName];
							}
						}
					}
					
					//分配执行规则中指定的参数
					$paramArray = self::decodePathinfoUrl(join('/',$exeUrlArray));
					$urlArray   = array_merge($urlArray,$paramArray);
					return $urlArray;
				}
			}
		}
		return $urlArray;
	}

	/**
	 * 路由编译
	 * 把数组编译为路由模式
	 *
	 * @return void
	 */
	public static function encodeRouteUrl($arr){
		if(!isset($arr[self::$c_name] ) || !isset($arr[self::$a_name]) || !($routeList = self::_getLeverRouteRule()) ){
			return false;
		}
		
		foreach($routeList as $level => $regArray){
			foreach($regArray as $regPattern => $value){
				$urlArray = explode('/',trim($value,'/'),3);
				if($level == 0 && ($arr[self::$m_name].'/'.$arr[self::$c_name].'/'.$arr[self::$a_name] != $urlArray[0].'/'.$urlArray[1].'/'.$urlArray[2])){
					continue;
				}else if($level == 1 && ($arr[self::$a_name] != $urlArray[1])){
					continue;
				}else if($level == 2 && ($arr[self::$c_name] != $urlArray[0])){
					continue;
				}
				
				$url = self::_parseRegPattern($arr,array($regPattern => $value));
				var_dump($url);die;
				if($url){
					return $url;
				}
			}
		}
		return false;
	}
	

	/**
	 * pathinfo解析
	 * 将/index/read/id/100形式的url转成数组的形式
	 * @param string $url
	 * @return array
	 */
	public static function decodePathinfoUrl($url){
		$data = array();
		preg_match("!^(.*?)?(\\?[^#]*?)?(#.*)?$!",$url,$data);
		$re = array();
		if(isset($data[1]) && trim($data[1],"/ ")){
			$string = explode("/", trim($data[1],"/ "));
			
			//前两个是ctrl和action，后面的是参数名和值
			$re[self::$m_name] = array_shift($string);
			$re[self::$c_name] = array_shift($string);
			$re[self::$a_name] = array_shift($string);
			
			//剩余参数自动按对拆分
			$otherArray = array_chunk($string,2);
			foreach($otherArray as $value){
				//key和value匹配正确
				if(count($value) == 2){
					//url存在数组格式类型
					if(strpos($value[0],"[") !== false){
						$urlArray = explode("[",$value[0]);
						$re[$urlArray[0]][trim($urlArray[1],"[]")] = $value[1];
					}else{
						$re[$value[0]] = $value[1];
					}
				}
			}
		}
		if(isset($data[2]) || isset($data[3])){
			$re[self::$route_config['ROUTE_MARK_KEY']] = ltrim($data[2],"?");
		}

		if(isset($data[3])){
			$re[self::$route_config['ROUTE_ANCHOR']] = ltrim($data[3],"#");
		}

		$re = self::_sortUrlArray($re);
		return $re;
	}	

	/**
	 * pathinfo编译
	 * 把数组编译为pathinfo模式如：/index/read/id/100
	 *
	 * @return void
	 */
	public static function encodePathinfoUrl($arr){
		$re = "";
		$mod	= isset($arr[self::$m_name])   ? $arr[self::$m_name]   : '';
		$ctrl	= isset($arr[self::$c_name])   ? $arr[self::$c_name]   : '';
		$action	= isset($arr[self::$a_name]) ? $arr[self::$a_name] : '';

		$mod    != "" && ($re.="/{$mod}");
		$ctrl   != "" && ($re.="/{$ctrl}");
		$action != "" && ($re.="/{$action}");

		$fragment = isset($arr[self::$route_config['ROUTE_ANCHOR']]) ? $arr[self::$route_config['ROUTE_ANCHOR']] : "";
		$questionMark = isset($arr[self::$route_config['ROUTE_MARK_KEY']]) ? 
			$arr[self::$route_config['ROUTE_MARK_KEY']] : "";
		unset($arr[self::$m_name],$arr[self::$c_name],$arr[self::$a_name],$arr[self::$route_config['ROUTE_ANCHOR']]);
		foreach($arr as $key=>$value){
			$re.="/{$key}/{$value}";
		}
		if($questionMark != ""){
			$re .= "?". $questionMark;
		}
		$fragment != "" && ($re .= "#{$fragment}");
		return $re;		
	}
	
	
	/**
	 * 路由重新分级
	 * @return array
	 */
	protected static function _getLeverRouteRule(){
		
		$cacheRoute = array();
		foreach(self::$route_rule as $key => $val){
			$tempArray = explode('/',trim($val,'/'),3);
			//进行路由规则的级别划分,$level越低表示匹配优先
			$level = 3;
			if(($tempArray[0] != '<'.self::$c_name.'>') && ($tempArray[1] != '<'.self::$a_name.'>'))$level = 0;
			elseif(($tempArray[0] == '<'.self::$c_name.'>') && ($tempArray[1] != '<'.self::$a_name.'>'))$level = 1;
			elseif(($tempArray[0] != '<'.self::$c_name.'>') && ($tempArray[1] == '<'.self::$a_name.'>')) $level = 2;
			$cacheRoute[$level][$key] = $val;
		}

		if(empty($cacheRoute)){
			self::$urlRoute = FALSE;
			return NULL;
		}

		ksort($cacheRoute);
		self::$route_rule = $cacheRoute;
		return self::$route_rule;
	}
	
	/**
	 * 对Url数组里的数据进行排序
	 * ctrl和action最靠前，其余的按key排序
	 * @param array $re
	 */
	protected static function _sortUrlArray($re){
		$fun_re=array();
		isset( $re[self::$m_name] ) && ($fun_re[self::$m_name]=$re[self::$m_name]);
		isset( $re[self::$c_name] ) && ($fun_re[self::$c_name]=$re[self::$c_name]);
		isset( $re[self::$a_name] ) && ($fun_re[self::$a_name]=$re[self::$a_name]);
		unset($re[self::$m_name],$re[self::$c_name],$re[self::$a_name]);
		ksort($re);
		$fun_re = array_merge($fun_re,$re);
		return $fun_re;
	}
	
	/**
	 * @brief 根据规则生成URL
	 * @param $urlArray array url信息数组
	 * @param $regPattern array 路由规则
	 * @return string or false
	 */
	protected static function _parseRegPattern($urlArray,$regArray){
		$regPattern = key($regArray);
		$value      = current($regArray);

		//存在自定义正则式
		if(preg_match_all("%<\w+?:.*?>%",$regPattern,$customRegMatch)){
			$regInfo = array();
			foreach($customRegMatch[0] as $val){
				$val     = trim($val,'<>');
				$regTemp = explode(':',$val,2);
				$regInfo[$regTemp[0]] = $regTemp[1];
			}

			//匹配表达式参数
			$replaceArray = array();
			foreach($regInfo as $key => $val){
				if(strpos($val,'%') !== false){
					$val = str_replace('%','\%',$val);
				}

				if(isset($urlArray[$key]) && preg_match("%$val%",$urlArray[$key])){
					$replaceArray[] = $urlArray[$key];
					unset($urlArray[$key]);
				}else{
					return false;
				}
			}

			$url = str_replace($customRegMatch[0],$replaceArray,$regPattern);
		}else{
			$url = $regPattern;
		}

		//处理多余参数
		$paramArray      = self::decodePathinfoUrl($value);
		
		$questionMarkKey = isset($urlArray[self::$route_config['ROUTE_MARK_KEY']]) ? 
			$urlArray[self::$route_config['ROUTE_MARK_KEY']] : '';
			
		$anchor = isset($urlArray[self::$route_config['ROUTE_ANCHOR']]) ? 
				$urlArray[self::$route_config['ROUTE_ANCHOR']]: '';
				
		unset(
			$urlArray[self::$c_name],$urlArray[self::$a_name],
			$urlArray[self::$route_config['ROUTE_ANCHOR']],
			$urlArray[self::$route_config['ROUTE_MARK_KEY']]
		);
		
		foreach($urlArray as $key => $rs){
			if(!isset($paramArray[$key])){
				$questionMarkKey .= '&'.$key.'='.$rs;
			}
		}
		$url .= ($questionMarkKey) ? '?'.trim($questionMarkKey,'&') : '';
		$url .= ($anchor)          ? '#'.$anchor                    : '';

		return $url;
	}
	
	/**
	 * 重新设置$_GET变量
	 *
	 * @param $url url链接
	 * @return void
	 */
	protected static function _reSetGet($url){
		$_GET[self::$m_name] = "";
    		$_GET[self::$c_name] = "";
    		$_GET[self::$a_name] = "";
    		
		//常规解析
		$beforeUrl = strstr($url,"?",true);
		if($beforeUrl){
			$parse = parse_url($url);
			if(isset($parse['query']) && $parse['query']){
				parse_str($parse['query'],$getParam);
			}
			$_GET = $getParam;
			//去掉?param=value的前置URL
			$url = $beforeUrl;
		}
		
		preg_match('/\.php(.*)/',$url,$phpurl);
		if(!isset($phpurl[1]) || !$phpurl[1]){
			return;
		}
		$url = $phpurl[1];
		
		
		$url_type = isset(self::$route_config['URL_TYPE']) ? self::$route_config['URL_TYPE'] : 'url';
		//路由规则解析
		$urlArray = array();
		if($url_type == 'route'){
			$urlArray = self::decodeRouteUrl($url);
			$_GET = !empty($urlArray) ? $urlArray : $_GET;
		}
		
		//PathInfo解析
		if($urlArray == array()){
			if($url[0] !== '?'){
				$urlArray = self::decodePathinfoUrl($url);
				
			}
			$_GET = !empty($urlArray) ? $urlArray : $_GET;
		}
	}
    
}
?>
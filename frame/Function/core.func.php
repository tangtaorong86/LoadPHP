<?php
/**
 * 实例化数据操作类，不用查找是否具有模型
 *
 * @param $table 数据表名
 * @return void 
 */
function table($table=""){
	if(empty($table)) return new Core\Model;
	return new Core\Model($table);
}

/**
 * 实例化模型操作类，模型名称默认为数据表名称
 * @param $model 模型名称
 * @return void
 */
function model($model=""){
	if(empty($model)) return new Core\Model;
	return \Core\Factory::getModel($model);
}

/**
 * 获取配置
 * @param $mod 模块名称
 * @return string
 */
function config($mod){
	if(!empty($mod)){
		$modx = explode(".",$mod);
		if(count($modx) == 1){
			return \Core\Factory::getConfig($mod);
		}else{
			$config = \Core\Factory::getConfig($modx[0]);
			return $config[$modx[1]];
		}
	}else{
		throw new \Exception("没找到配置信息！");
	}
}

/**
 * 生产url
 *
 * @return string
 */
// url("home/index","fasdf=fasd&fasdf=fasd");
function url($mca,$param){
	$mcax = array();
	list($mcax[config("app.VAR_MODULE")],$mcax[config("app.VAR_CONTROLLER")],$mcax[config("app.VAR_ACTION")]) = 
	explode("/", $mca);
	if(!is_array($param)){
		$query = explode("&",$param);
		$param = array();
		foreach($query as $value){
			$tmp = explode("=",$value);
			if(count($tmp) == 2){
				$param[$tmp[0]] = $tmp[1];
			}
		}
	}
	$urlArray = array_merge($mcax,$param);
	$Route = new \Core\Route();
	return $Route->makeUrl($urlArray);
}

?>
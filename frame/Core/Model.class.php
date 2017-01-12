<?php
/**
 * 模型的父类
 *
 * @package Model
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */

namespace Core;
class Model{
	/**
	 * 相关配置
	 *
	 * @var array
	 */
	protected $config = array();
	
	/**
	 * 驱动对象
	 *
	 * @var string
	 */
	protected $db = array();
	
	/**
	 * 模型名称
	 *
	 * @var string
	 */
	protected $modelName = '';
	
	/**
	 * 操作表
	 *
	 * @var string
	 */
	protected $tableName = '';
	
	/**
	 * 自定义的操作表
	 *
	 * @var string
	 */
	protected $tableNameDiy = '';
	
	/**
	 * 操作参数
	 *
	 * @var array
	 */
	protected $param = array(
		'table' => '',
		'field' => '*',
		'where' => array(),
		'order' => '',
		'limit' => '',
		'data'  => array(),
		'cache' => 0
	);
	
	public function __construct($name=""){
		$this->config = Factory::getConfig("db");
		//找到数据库驱动(这里不需要做成单例模式了，工厂方法里已经做了防止重复初始化)
		$this->db = Factory::getDriver($this->config['DB_TYPE'],"Db");
		// 模型名称
		$this->modelName = empty($name) ? $this->_getModelName() : $name;
		// 数据表名称
		$this->tableName = $this->_getTableName();
	}
	
	/**
	 * 执行sql的读操作
	 *
	 * @param $sql
	 * @param $bindParam 绑定参数
	 * @return intval 
	 * @author tangtaorong86
	 */
	public function read($sql,array $bindParam = array()){
		$sql = trim($sql);
		if(empty($sql))return array();
		$sql = $this->_parseSql($sql);
		return $this->db->read($sql,$bindParam);
	}
	
	/**
	 * 执行sql的写操作
	 *
	 * @param $sql
	 * @param $bindParam 绑定参数
	 * @return intval 影响行数
	 * @author tangtaorong86
	 */
	public function write($sql,array $bindParam = array()){
		$sql = trim($sql);
		if(empty($sql))return 0;
		$sql = $this->_parseSql($sql);
		return $this->db->write($sql,$bindParam);
	}
		
	
	/**
	 * 查询单个
	 *
	 * @return void
	 * @author tangtaorong86 
	 */
	public function getOne() {
		$condition = $this->param['where'];
		$field = $this->param['field'];
		$order = $this->param['order'];
		
		// 判断查询缓存
        if(isset($param['cache'])){
            $cache  =   $param['cache'];
            $key    =   is_string($cache['key'])?$cache['key']:md5(serialize($param));
            $data   =   S($key,'',$cache);
            if(false !== $data){
                return $data;
            }
        }
		     
		return $this->db->getOne($this->tableName,$condition,$field,$order);
	}
	
	/**
	 * 查询单个
	 *
	 * @return void
	 * @author tangtaorong86 
	 */
	public function getList(){
		$condition = $this->param['where'];
		$field = $this->param['field'];
		$order = $this->param['order'];
		$limit = $this->param['limit'];
		return $this->db->getList($this->tableName,$condition,$field,$order,$limit);
	}
	
	/**
	 * 查询数量
	 *
	 * @return void
	 * @author tangtaorong86 
	 */
	public function count(){
		if(!is_array($this->param['where']) ) return false;
		$condition = $this->param['where'];
		return $this->db->count($this->tableName,$condition);
	}
	
	/**
	 * 新增数据
	 *
	 * @return void
	 * @author tangtaorong86 
	 */
	public function insert(){
		if(empty($this->param['data']) || !is_array($this->param['data']) ) return false;
		$data = $this->param['data'];
		return $this->db->insert($this->tableName,$data);
	}
	
	/**
	 * 修改数据
	 *
	 * @return void
	 * @author tangtaorong86 
	 */
	public function update(){
		if(empty($this->param['data']) || !is_array($this->param['data']) ) return false;
		if(empty($this->param['where']) || !is_array($this->param['where']) ) return false;
		$data = $this->param['data'];
		$condition = $this->param['where'];
		return $this->db->update($this->tableName,$condition,$data);
	}
	
	/**
	 * 删除数据
	 *
	 * @return void
	 * @author tangtaorong86 
	 */
	public function delete(){
		if(empty($this->param['where']) || !is_array($this->param['where']) ) return false;
		$condition = $this->param['where'];
		return $this->db->delete($this->tableName,$condition);
	}
	
	/**
	 * 事务开始
	 * @return boolean
	 * @author tangtaorong86 
	 */
	public function beginTransaction(){
		return $this->db->beginTransaction();
	}
	
	/**
	 * 事务提交
	 * @return boolean
	 * @author tangtaorong86 
	 */
	public function commit(){
		return $this->db->commit();
	}
	
	/**
	 * 事务回滚
	 * @return boolean
	 * @author tangtaorong86
	 */
	public function rollBack(){
		return $this->db->rollBack();
	}
	
	
	/**
	 * 设置数据表
	 *
	 * @param $table 表名称
	 * @param $pre 表前缀
	 * @return object
	 * @author tangtaorong86 
	 */
	public function table($table,$pre=""){
		$pre = $pre == "" ? $this->config['DB_PREFIX'] : $pre;
		$this->param['table'] = $pre.$table;
		return $this;
	}
		
	
	/**
	 * 设置查询字段
	 *
	 * @param $field 表名称
	 * @return object
	 * @author tangtaorong86 
	 */
	public function field($field){
		$this->param['field'] = $field;
		return $this;
	}
	
	/**
	 * 设置查询条件
	 *
	 * @param $where 查询条件 ：$where = array("id=:did",array(":did"=>1));
	 * @return object
	 * @author tangtaorong86 
	 */
	public function where($where){
		//需要去除无关的绑定参数
		$this->param['where'] = $where;
		return $this;
	}
	
	/**
	 * 设置排序条件
	 *
	 * @param $order 排序条件
	 * @return object
	 * @author tangtaorong86
	 */
	public function order($order){
		$this->param['order'] = $order;
		return $this;
	}
	
	/**
	 * 设置查询条数
	 *
	 * @param $limit 条数
	 * @return object
	 * @author tangtaorong86 
	 */
	public function limit($offset,$length=null){
        if(is_null($length) && strpos($offset,',')){
            list($offset,$length) = explode(',',$offset);
        }
        $this->param['limit'] = intval($offset).( $length? ','.intval($length) : '' );
        return $this;
    }

	
	/**
	 * 设置写入数据
	 *
	 * @param $data 写入数据(新增或者更新)
	 * @return object
	 * @author tangtaorong86 
	 */
	public function data($data){
		if('' === $data && !empty($this->param['data'])) {
            return $this->data;
        }
        if(is_object($data)){
            $data = get_object_vars($data);
        }elseif(is_string($data)){
            parse_str($data,$data);
        }elseif(!is_array($data)){
			throw new \Exception('data数据格式错误 ', 500);
        }
		$this->param['data'] = $data;
		return $this;
	}
	
	/**
	 * 设置缓存
	 *
	 * @param $time 缓存时间(新增或者更新)
	 * @param $type 缓存方式
	 * @return object
	 * @author tangtaorong86
	 */
	public function cache($time=0,$type="File"){
		
//		$this->_getCacheDriver($type)->
		$this->param['cache'] = $cache;
		return $this;
	}
	
	/**
	 * 删除缓存
	 *
	 * @return void
	 * @author  
	 */
	public function clear(){
		
	}
	
	/**
	 * 输出sql
	 *
	 * @return $sql
	 * @author tangtaorong86 
	 */
	public function sql(){
		return $this->db->getSql();
	}
	
	/**
	 * 获取缓存驱动
	 *
	 * @return void
	 * @author tangtaorong86
	 */
	private function _getCacheDriver($type=""){
		$cacheConfig = \Core\Factory::getConfig("cache");
		$cacheType = $type == "" ? $cacheConfig['DATA_CACHE_TYPE'] : $type;
		return Factory::getDriver($cacheType,"Cache");
	}
	
	/**
	 * 解析sql里的前缀常量
	 *
	 * @return string $sql
	 * @author tangtaorong86
	 */
	private function _parseSql($sql){
		return strtr($sql,array('__PRE__'=>$this->config['DB_PREFIX']));
	}
	
	/**
     * 得到当前的数据对象名称
	 * 
     * @return string
	 * @author tangtaorong86
     */
    private function _getModelName() {
        if(empty($this->modelName)){
            $name = substr(get_class($this),0,-strlen(config("APP.MODEL_SUFFIX")));
            if ( $pos = strrpos($name,'\\') ) {//有命名空间
                $this->modelName = substr($name,$pos+1);
            }else{
                $this->modelName = $name;
            }
        }
        return $this->modelName;
    }
	
	 /**
     * 得到完整的数据表名
	 * 
     * @return string
     * @author tangtaorong86
     */
    private function _getTableName() {
    		$table = $this->param['table'];
    		if(empty($table)){
    			//无，再看是否自定义了tableNameDiy
    			$table = $this->tableNameDiy;
				if(empty($table)){
					//无，再获取模型名称，拼接上前缀作为数据表名
					$table = $this->modelName;
				}
    		}
		
		if(empty($table)){
			return NULL;
		}
		
    	return $this->config['DB_PREFIX'].$table;
    }
	
}

?>
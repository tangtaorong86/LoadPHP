<?php
/**
 * Mysql操作类（此处不需要单例模式了，在工厂方法里已经做了防止重复实例化）
 *
 * @package default
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */
namespace Driver\Db;
use \PDO;
use Driver\DbInterface;
class Mysql implements DbInterface{
	
	protected $config =array();
	protected $_writeLink = NULL;
	protected $_readLink = NULL;
	public $bing_sql;
	public $bind_param;
	
	public function __construct(){
		$this->config = \Core\Factory::getConfig("db");
	}
	
	/**
	 * 获取多条数据
	 * 
	 * @param $table
	 * @param $condition 
	 * 格式：array(
	 * 	"name=:name and id=:id",//where条件
	 * 	array(":name"=>"test",":id"=>1)//需要绑定的参数
	 * );
	 * @param $field
	 * @param $order
	 * @param $limit
	 * @return array()
	 * @author tangtaorong86
	 */
	public function getList($table,array $condition = array(),$field="*",$order=NULL,$limit=NULL){
		$where = !empty($condition) ? ' WHERE '.$condition[0] : '';
		$bind_param  = !empty($condition) ? $condition[1] : array();
		
		$field = !empty($field) ? $field : '*';
		$order = !empty($order) ? ' ORDER BY '.$order : '';
		$limit = !empty($limit) ? ' LIMIT '.$limit : '';
		
		$sql = "select {$field} from `{$table}` {$where} {$order} {$limit}";
		return $this->read($sql,$bind_param);
	}
	
	/**
	 * 获取单条数据
	 * 
	 * @param $table
	 * @param $condition 
	 * 格式：array(
	 * 	"name=:name and id=:id",//where条件
	 * 	array(":name"=>"test",":id"=>1)//需要绑定的参数
	 * );
	 * @param $field
	 * @param $order
	 * @return array()
	 * @author tangtaorong86
	 */
	public function getOne($table,array $condition = array(),$field="*",$order=NULL){
		$where = !empty($condition) ? ' WHERE '.$condition[0] : '';
		$bind_param  = !empty($condition) ? $condition[1] : array();
		
		$field = !empty($field) ? $field : '*';
		$order = !empty($order) ? ' ORDER BY '.$order : '';
		
		$sql = "select {$field} from `{$table}` {$where} {$order} limit 1";
		$link = $this->_executeSql($sql,$bind_param,$this->_getReadLink());
		return $link->fetch(\PDO::FETCH_ASSOC);
	}	
	
	/**
	 * 执行读操作
	 *
	 * @return array()
	 * @author tangtaorong86
	 */
	public function read($sql,array $bind_param = array()){
		$link = $this->_executeSql($sql,$bind_param,$this->_getReadLink());
		return $link->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	/**
	 * 执行写操作
	 *
	 * @return int 影响行数
	 * @author tangtaorong86
	 */
	public function write($sql,array $bind_param = array()){
		$link = $this->_executeSql($sql,$bind_param,$this->_getWriteLink());
		return $link->rowCount();
	}
	
	/**
	 * 写入数据
	 *
	 * @return lastid
	 * @author tangtaorong86
	 */
	public function insert($table,array $data = array()){
		$bind_param = array();
		foreach($data as $k=>$v){
			$keys[] = "`{$k}`"; 
			$marks[] = ":{$k}";
			$bind_param[":{$k}"] = $v; 
		}
		$sql = "INSERT INTO {$table} (".implode(', ', $keys).") VALUES (".implode(', ', $marks).")";
		$status = $this->_executeSql($sql,$bind_param,$this->_getWriteLink());
		$id = $this->_getWriteLink()->lastInsertId();
		if($id){
			return $id;
		}else{
			return $status;
		}
	}
	
	/**
	 * 数据更新
	 *
	 * @return 影响行数
	 * @author tangtaorong86  
	 */
	public function update($table,array $condition = array(),array $data = array()) {
		$where = !empty($condition) ? ' WHERE '.$condition[0] : '';
		$bind_param_condition  = !empty($condition) ? $condition[1] : array();
		
		$bind_param_value = array();
		foreach($data as $k=>$v){
			$keys[] = "`{$k}`=:__{$k}";
			$bind_param_value[":__{$k}"] = $v;	
		}
		
		$bind_param = array_merge($bind_param_condition,$bind_param_value);
		$sql = "UPDATE {$table} SET ".implode(', ', $keys) . $where;
		return $this->write($sql,$bind_param);
	}
	
	/**
	 * 删除数据
	 *
	 * @return 操作记录
	 * @author tangtaorong86
	 */
	public function delete($table,array $condition = array()){
		$where = !empty($condition) ? ' WHERE '.$condition[0] : '';
		$bind_param = !empty($condition) ? $condition[1] : array();
		$sql = "DELETE FROM {$table} {$where}";
		return $this->write($sql,$bind_param);
	}
	
	/**
	 * 获取数量
	 *
	 * @return 数量
	 * @author tangtaorong86
	 */
	public function count($table,array $condition = array()){
		$where = !empty($condition) ? ' WHERE '.$condition[0] : '';
		$bind_param = !empty($condition) ? $condition[1] : array();
		$sql = "select count(*) as num from `{$table}` {$where}";
		$link = $this->_executeSql($sql,$bind_param,$this->_getReadLink());
		$res = $link->fetch(\PDO::FETCH_ASSOC);
		return isset($res['num']) ? intval($res['num']) : 0;
	}
	
	/**
	 * 事务开始
	 * @return boolean
	 */
	public function beginTransaction(){
		return $this->_getWriteLink()->beginTransaction();
	}
	
	/**
	 * 事务提交
	 * @return boolean
	 */
	public function commit(){
		return $this->_getWriteLink()->commit();
	}
	
	/**
	 * 事务回滚
	 * @return boolean
	 */
	public function rollBack(){
		return $this->_getWriteLink()->rollBack();
	}

	/**
	 * 获取执行的sql
	 *
	 * @return 最好执行的sql语句
	 * @author tangtaorong86
	 */
	public function getSql(){
		$bind_sql = $this->bind_sql;
		$bind_param = $this->bind_param;
		$i = 0;
		$ret = preg_replace_callback('/:([0-9a-z_]+)|\?+/i', function($m)use($bind_param,&$i){
			$k = array_keys($bind_param);
			$v = $m[0] == '?' ? $bind_param[$i] : (substr($k[$i], 0, 1) == ':' ? $bind_param[$m[0]] : $bind_param[$m[1]]);
			if($v === null)return "NULL";
			if(!is_numeric($v))$v = "'{$v}'";
			$i++;
			return $v;
		}, $bind_sql);
		return $ret;		
	}
	
	/**
	 * sql预处理
	 *
	 * @return void
	 * @author tangtaorong86
	 */
	protected function _executeSql($sql,$data,$link){
		$this->bind_sql = $sql;
		$this->bind_param = $data;
		$res = $link->prepare($sql);
		if($res->execute($data)){
			return $res;
		}else{
			$error = $res->errorInfo();
			\Core\Factory::showSqlError($this->getSql(),$error);
		}
	}

	/**
	 * 连接mysql
	 *
	 * @return void
	 * @author  
	 */				
	protected  function _connect($isMaster = true) {
		$dbConfig = array();
		if( false==$isMaster && !empty($this->config['DB_SLAVE']) ) {	
			$master = $this->config;
			unset($master['DB_SLAVE']);
			foreach($this->config['DB_SLAVE'] as $k=>$v) {
				$dbConfig[] = array_merge($master, $this->config['DB_SLAVE'][$k]);
			}
			shuffle($dbConfig);
		}else{
			$dbConfig[] = $this->config;
		}
		
		try {
			$dsn = 'mysql:host='.$dbConfig[0]['DB_HOST'].';port='.$dbConfig[0]['DB_PORT'].';dbname='.$dbConfig[0]['DB_NAME'];
			$link = new PDO(
				$dsn,
				$dbConfig[0]['DB_USER'], $dbConfig[0]['DB_PWD'],
				array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$dbConfig[0]['DB_CHARSET'])
			);
			$link->exec('set names '.$dbConfig[0]['DB_CHARSET']);
	        return $link;
		}catch(PDOException $e) {
			throw new \Exception('connect database error :'.$e->getMessage(), 500);
			exit();
		}
	}

    protected function _getReadLink() {
		if(!isset($this->_readLink)) {
			try{
				$this->_readLink = $this->_connect( false );
			}catch(Exception $e){
				$this->_readLink = $this->_getWriteLink();
			}			
		}
		return $this->_readLink;
    }
	
    protected function _getWriteLink() {
        if(!isset($this->_writeLink)) {
            $this->_writeLink = $this->_connect( true );
        }
		return $this->_writeLink;
    }
	
	public function __destruct() {
		if($this->_writeLink) {
			$this->_writeLink = NULL;
		}
		if($this->_readLink) {
			$this->_readLink = NULL;
		}
	}
	
}
?>
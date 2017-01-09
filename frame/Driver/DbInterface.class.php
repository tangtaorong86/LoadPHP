<?php
/**
 * 数据库接口
 *
 * @package default
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */

namespace Driver;
interface DbInterface{
	
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
	public function getList($table,array $condition = array(),$field="*",$order=NULL,$limit=NULL);
	
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
	public function getOne($table,array $condition = array(),$field="*",$order=NULL);
	
	/**
	 * 执行读操作
	 *
	 * @return array()
	 * @author tangtaorong86
	 */
	public function read($sql,array $bind_param = array());
	
	/**
	 * 执行写操作
	 *
	 * @return int 影响行数
	 * @author tangtaorong86
	 */
	public function write($sql,array $bind_param = array());
	
	/**
	 * 写入数据
	 *
	 * @return lastid
	 * @author tangtaorong86
	 */
	public function insert($table,array $data = array());
	
	/**
	 * 数据更新
	 *
	 * @return 影响行数
	 * @author tangtaorong86  
	 */
	public function update($table,array $condition = array(),array $data = array());
	
	/**
	 * 删除数据
	 *
	 * @return 操作记录
	 * @author tangtaorong86
	 */
	public function delete($table,array $condition = array());

	/**
	 * 获取数量
	 *
	 * @return 数量
	 * @author tangtaorong86
	 */
	public function count($table,array $condition = array());
	
	/**
	 * 获取最后执行sql
	 * @return string
	 */
	public function getSql();
	
	/**
	 * 事务开始
	 * @return boolean
	 */
	public function beginTransaction();
	
	/**
	 * 事务提交
	 * @return boolean
	 */
	public function commit();
	
	/**
	 * 事务回滚
	 * @return boolean
	 */
	public function rollBack();
	
	/**
	 * 析构函数
	 * @return boolean
	 */
	public function __destruct();
}

?>
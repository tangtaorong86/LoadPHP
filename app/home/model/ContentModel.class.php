<?php
namespace app\home\model;
use Core\Model;
class ContentModel extends Model{
	
	protected $tableNameDiy = "test";
	
	public function getContent(){
		$where = array();
		$where[] = "id=:test and name=:name";
		$where[] = array(":test"=>2,":name"=>"我是标题");
		$res = $this->where($where)->getOne();
		echo $this->sql();
		var_dump($res);
	}
		
}

?>
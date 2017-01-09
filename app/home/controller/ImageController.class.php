<?php
namespace app\home\controller;
use Core\Controller;

class IndexController extends Controller{
	
	public function index(){
		$if = 1;
		$this->assign("test",$if);
		
		$arr = array(1,2,3,4,5,6);
		$this->assign("arr",$arr);
		
		$aaa = "ddd";
		$this->assign("aaa",$aaa);
		$this->display();
	}
	
	public function list(){
		
	}
	
	public function find(){
		$Content = Model("Content");
		$Content->getContent();
	}
	
	public function insert(){
		
	}
	
	public function update(){
		
	}

	public function delete(){
		
	}
	
	public function _empty(){
		echo "我是空的";
	}
	
}

?>
<?php
namespace app\home\controller;
use Core\Controller;
use Library\Page;
class IndexController extends Controller{
	
	const UrlCtrlName	= 'controller';
	const UrlActionName	= 'action';
	
	const Anchor = "/#&"; //urlArray中表示锚点的索引
	const QuestionMarkKey = "?";// /site/abc/?callback=/site/login callback=/site/login部分在UrlArray里的key
	
	private static $urlRoute = array(); //路由规则的缓存
	
	public function index(){
		$url = url("home/index/lists",array("p"=>2));
		echo "<a href='{$url}'>列表页</a>";
		$if = 1;
		$this->assign("test",$if);
		
		$arr = array(1,2,3,4,5,6);
		$this->assign("arr",$arr);
		
		$aaa = "ddd";
		$this->assign("aaa",$aaa);
		$this->display();
	}
	
	public function lists(){
		$Test = Table("test");
		$count = $Test->count();
		
		$page = new Page($count);
		$list = $Test->limit($page->limit)->getList();
		$pageHtml = $page->pageShow();
		$this->assign("list",$list);
		$this->assign("page",$pageHtml);
		$this->display();
	}
	
	public function sqlRead(){
		$sql = "select * from __PRE__test";
		$res = Table()->read($sql);
		dump($res);	
	}
	
	public function content(){
		$Content = Model("Content");
		$Content->getContent();
	}
	
	public function insert(){
		$data = array();
		$data['name'] = "我是标题".time();
		$data['content'] = "我是内容".time();
		$res = Table("test")->data($data)->insert();
		var_dump($res);
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
<?php
namespace app\home\controller;
use Core\Controller;
use Library\Captcha;
class CaptchaController extends Controller{
	
	public function index(){
		
		if(IS_POST){
			$vc = new Captcha();
			$res = $vc->check($_POST['verify']);
			var_dump($res);
		}else{
			$this->display();
		}
	}
	
	public function show(){
		$vc = new Captcha();
		$vc->show();
	}
	
	public function _empty(){
		echo "我是空的";
	}
	
}

?>
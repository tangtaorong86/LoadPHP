<?php
/**
 * 控制器父类
 *
 * @package Controller
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */

namespace Core;
abstract class Controller{
    protected $view;
	
	protected $config = array();

	/**
	 * 构造方法
	 *
	 * @return void
	 * @author  
	 */
    public function __construct(){
    		$this->config = config("app");
    		$this->view = new View();
    }

	/**
	 * 引入变量
	 *
	 * @param string $key 模板变量
	 * @param string $value 脚本变量
	 * @return void
	 * @author  
	 */
    public function assign($key, $value){
        $this->view->assign($key,$value);
    }

	/**
	 * 渲染模板
	 *
	 * @param string $file 模板文件名
	 * @return void
	 * @author  
	 */
    public function display($file = ''){
    		$this->view->display($file);
    }
	
	/**
	 * tplContent
	 *
	 * @return void
	 * @author  
	 */
	public function tplContent(){
		$this->view->fetch($file);
	}
	
	 /**
     * 操作成功
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    public function success($message,$jumpUrl='',$ajax=false) {
        $this->_showTipsTpl($message,$jumpUrl,$ajax,1);
    }
    
     /**
     * 操作失败
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    public function error($message,$jumpUrl='',$ajax=false) {
        $this->_showTipsTpl($message,$jumpUrl,$ajax,0);
    }
	
/**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @param int $json_option 传递给json_encode的option参数
     * @return void
     */
    protected function ajaxReturn($data,$type='',$json_option=0) {
        if(empty($type)) $type  =   $this->config['DEFAULT_AJAX_RETURN'];
        switch (strtoupper($type)){
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data,$json_option));
            case 'XML'  :
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                $handler  =   isset($_GET[$this->config['VAR_JSONP_HANDLER']]) ? $_GET[$this->config['VAR_JSONP_HANDLER']] : $this->config['DEFAULT_JSONP_HANDLER'];
                exit($handler.'('.json_encode($data,$json_option).');');  
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);            
            default     :
                // 用于扩展其他返回格式数据
                return;
        }
    }
	
	/**
	 * 跳转操作
	 *
	 * @return void
	 * @author  
	 */
	private function _showTipsTpl($message,$jumpUrl='',$ajax=false,$status=1){
		if(true === $ajax || IS_AJAX) {// AJAX提交
            $data           =   is_array($ajax) ? $ajax : array();
            $data['info']   =   $message;
            $data['status'] =   $status;
            $data['url']    =   $jumpUrl;
            $this->ajaxReturn($data);
        }
        // 成功操作后默认停留1秒
        $waitSecond = is_int($ajax) ? $ajax : 1;
		$jumpUrl = !isset($jumpUrl) ? (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "") : "";
		$tpl = $status ? $this->config['TIPS_TPL_SUCCESS'] : $this->config['TIPS_TPL_ERROR'];
        include_once($tpl);
		exit;
	}
    
}
?>
<?php
/**
 * 默认模板引擎
 *
 * @package Default
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */

namespace Driver\Tpl;
use Driver\TplInterface;
class Normal implements TplInterface{
	
    /**
     * 模板引擎组合后文件存放目录
     *
     * @var string
     */
    public $compile_dir;
    
    /**
     * 左定界符
     *
     * @var string
     */
    public $left_delimiter; 
    
    /**
     * 右定界符
     *
     * @var string
     */
    public $right_delimiter;
    
    /**
     * 内部使用的临时变量
     *
     * @var string
     */
    private $tpl_vars = array();
    
    /**
     * 模板配置
     *
     * @var string
     */
    private $tpl_config = array();
    
    /**
     * 模板文件
     *
     * @var string
     */
    private $tpl_file;
    
    /**
     * 构造方法
     *
     * @return void
     */
    public function __construct(){
    		$this->tpl_config = \Core\Factory::getConfig("tpl");
    		$this->compile_dir = $this->tpl_config['TPL_CACHE_PATH'];
    		$this->left_delimiter = $this->tpl_config['TPL_LEFT_DELIMITER'];
    		$this->right_delimiter = $this->tpl_config['TPL_RIGHT_DELIMITER'];
		if(!file_exists($this->compile_dir)){
			if(!dmkdir($this->compile_dir,0755,TRUE)){
				throw new IException('模板缓存目录创建失败！请检查'.$this->compile_dir.'目录的读写状态！');
			}		
		}
    }
  
    /**
     * 将php中分配的值保存到成员属性$tpl_vars中，用于模板中对应的变量进行替换  
     *
     * @param string $tpl_var 模板变量
     * @param string $value 脚本变量
     * @return void  
     */
    public function assign($tpl_var,$value = null){  
        if($tpl_var != '')$this->tpl_vars[$tpl_var] = $value;  
    }

    /**
     * 渲染模板直接输出
     *
     * @param string $tplFile 模板文件
     * @return void 
     */
    public function display($tplFile){
    		$this->_get_compile_content($tplFile);
    }
    
    /**
     * 渲染模板直接返回内容
     *
     * @return void
     * @author  
     */
    function functionName($tplFile){
    		$this->_get_compile_content($tplFile,1);
    }
	
	/**
	 * 包含模板
	 *
	 * @return void 
	 */
	private function _getTemplate($tpl){
		//找到模板
        if(array_key_exists(MODULE_NAME,$this->tpl_config['TPL_THEME_MODULE'])){
        		$theme = $this->tpl_config['TPL_THEME_MODULE'][MODULE_NAME];
        		$dir = $this->tpl_config['TPL_TEMPLATE_PATH'].$theme."/";
        }else{
        		$dir = $this->tpl_config['TPL_TEMPLATE_PATH'];
        }
        if (empty($file)){
            $file = $tpl.$this->tpl_config['TPL_CACHE_SUFFIX'];
        }
        $template_dir = APP_PATH."/".MODULE_NAME.$dir;
		$tplFile = $template_dir.$file;
    		return $this->_get_compile_content($tplFile,1);
	}
	
	/**
	 * 获取编译后的内容
	 *
	 * @return void
	 * @author  
	 */
	private function _get_compile_content($tplFile,$return=0) {
		if(!empty($this->tpl_vars))extract($this->tpl_vars);
        $comFileName = $this->compile_dir.md5($tplFile).'.php'; 
        //判断是否开启模板缓存
        if($this->tpl_config['TPL_CACHE_ON']){
	        if(!file_exists($comFileName) || filemtime($comFileName) < filemtime($tplFile)){
	            $repContent = $this->_compile($tplFile);
	            file_put_contents($comFileName,"<?php if(!defined('FRAME_VER')) exit;?>".$repContent); 
	        }
	        if($return){
       			return file_get_contents($comFileName);
       		}else{
       			require $comFileName;
       		}
        }else{
        		if($return){
        			return $this->_compile($tplFile);
        		}else{
        			eval('?>'.$this->_compile($tplFile));//直接执行编译后的模板输出
        		}
        }
	}
	
    /**
     * 正则替换
     *
     * @param $content 需要替换的内容
     * @return 替换后的字符串
     */
    private function _compile($tplFile){
    		
    		$template = file_get_contents($tplFile);
    		
        /*将左右定界符号中，有影响正则的特殊符号转义*/  
        $left = preg_quote($this->left_delimiter,'/');  
        $right = preg_quote($this->right_delimiter,'/');  
        
		//常量解析
		$template = preg_replace("/__[A-Z]+__/i", "<?php if(defined('$0')) echo $0; else echo '$0'; ?>", $template);//        
        
       /*将变量{$name}替换成</?/php  echo $name;/?/>*/
		$template = preg_replace("/".$left."(\\$[a-zA-Z_]\w*(?:\[[\w\.\"\'\[\]\$]+\])*)".$right."/i", "<?php echo $1; ?>", $template);//替换变量
		
		//解析模板包含
	    $template= preg_replace_callback("/".$left."include\s*file=\"(.*)\"".$right."/i",function($match){return $this->_getTemplate($match[1]);}, $template);//递归解析模板包含

		//php标签
		$template = preg_replace( "/".$left."eval\s+(.+)".$right."/", "<?php \\1?>", $template );
		
		//if 标签
		$template = preg_replace( "/".$left."if\s+(.+?)".$right."/", "<?php if(\\1) { ?>", $template );
		$template = preg_replace( "/".$left."else".$right."/", "<?php } else { ?>", $template );
		$template = preg_replace( "/".$left."elseif\s+(.+?)".$right."/", "<?php } elseif (\\1) { ?>", $template );
		$template = preg_replace( "/".$left."\/if".$right."/", "<?php } ?>", $template );
		
		//for 标签
		$template = preg_replace("/".$left."for\s+(.+?)".$right."/","<?php for(\\1) { ?>",$template);
		$template = preg_replace("/".$left."\/for".$right."/","<?php } ?>",$template);
		
		//loop 标签
		$template = preg_replace( "/".$left."foreach\s+(\S+)\s+(\S+)".$right."/", "<?php \$n=1;if(is_array(\\1)) foreach(\\1 AS \\2) { ?>", $template );
		$template = preg_replace( "/".$left."foreach\s+(\S+)\s+(\S+)\s+(\S+)".$right."/", "<?php \$n=1; if(is_array(\\1)) foreach(\\1 AS \\2 => \\3) { ?>", $template );
		$template = preg_replace( "/".$left."\/foreach".$right."/", "<?php \$n++;}unset(\$n); ?>", $template );
		
		//函数 标签
		$template = preg_replace ( "/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $template );
		$template = preg_replace ( "/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $template );
			
		//变量/常量 标签
		$template = preg_replace( "/".$left."([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)".$right."/s", "<?php echo \\1;?>", $template );	
       	
        return $template;     //返回替换后的字符串  
    }  

}  

?>
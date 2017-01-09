<?php
/**
 * 模板引擎适配器
 *
 * @package Tpl
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */
namespace Driver;
interface TplInterface
{
    /**
     * 引入变量
     * @param string $tpl_var 模板变量
     * @param string $value 脚本变量
     */
    public function assign($tpl_var,$value);
    
    /**
     * 渲染模板
     * @param string $filename 模板文件名
     * @return void
     */
    public function display($filename);
    
    
}
?>
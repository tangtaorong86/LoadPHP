<?php
/**
 * $_SERVER的兼容处理方案
 *
 * @package default
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */
namespace Driver;
interface ServerVarsInterface
{
    /**
     * requestUri
     */
    public function requestUri();
	
	/**
	 * 服务器执行的物理URI路径,包括入口index.php
	 */
	public function realUri();
	
}
?>
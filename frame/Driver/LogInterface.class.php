<?php
/**
 * 日志记录适配器
 *
 * @package default
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */
namespace Driver;
interface LogInterface
{
    /**
     * 实现日志的写操作接口
     * @param string $logs 日志的内容
     */
    public function write($logs = "");
}
?>
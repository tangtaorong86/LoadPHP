<?php
$config = array(
	'DATA_CACHE_TIME'       =>  0,      // 数据缓存有效期 0表示永久缓存
	'DATA_CACHE_TYPE'       =>  'File',  // 数据缓存类型,支持:File
	'DATA_CACHE_EXT'        =>  '.data', //缓存后缀，只对file缓存有效
	'DATA_CACHE_PATH'       =>  DATA_PATH.'/cache/',// 缓存路径设置 (仅对File方式缓存有效)
	'DATA_PATH_LEVEL'       =>  1,        // 子目录缓存级别
);
return $config;
<?php
$config = array(
	'DB_TYPE' => 'mysql',//数据库类型，一般不需要修改
	'DB_HOST' => 'localhost',//数据库主机，一般不需要修改
	'DB_USER' => 'root',//数据库用户名
	'DB_PWD' => 'root',//数据库密码
	'DB_PORT' => 3306,//数据库端口，mysql默认是3306，一般不需要修改
	'DB_NAME' => 'test_a',//数据库名
	'DB_CHARSET' => 'utf8',//数据库编码，一般不需要修改
	'DB_PREFIX' => 'pre_',//数据库前缀
	'DB_SLAVE'  => array(
		array(
			'DB_TYPE' => 'mysqli',//数据库类型，一般不需要修改
			'DB_HOST' => 'localhost',//数据库主机，一般不需要修改
			'DB_USER' => 'root',//数据库用户名
			'DB_PWD' => 'root',//数据库密码
			'DB_PORT' => 3306,//数据库端口，mysql默认是3306，一般不需要修改
			'DB_NAME' => 'test_a',//数据库名
			'DB_CHARSET' => 'utf8',//数据库编码，一般不需要修改
		)
	),
);
return $config;
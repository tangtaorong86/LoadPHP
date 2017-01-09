<?php
$config = array(
	//网址配置
	'URL_TYPE' => 'pathinfo',//url状态url:默认 pathinfo:pathinfo模式 route：路由模式
//				'URL_MODULE_DEPR' => '/',//模块分隔符，一般不需要修改
//				'URL_ACTION_DEPR' => '-',//操作分隔符，一般不需要修改
//				'URL_PARAM_DEPR' => '-',//参数分隔符，一般不需要修改
//				'URL_HTML_SUFFIX' => '.html',//伪静态后缀设置，例如 .html ，一般不需要修改
	'ROUTE_POWER' => FALSE,
	'ROUTE_ANCHOR' => "/#&", //锚点的索引
	'ROUTE_MARK_KEY' => "?", // 如/site/?callback=/site/login
	'ROUTE_RULE' => array(   //路由规则
		'list.html' => 'home/index/lists',
	  	'article-<id:\\d+>.html' => 'home/site/article_detail',
	  	'item-<id:\\d+>.html' => 'home/site/products',
	  	'list-<cat:\\d+>.html' => 'home/site/pro_list',
	  	'tuan.html' => 'home/site/groupon',
//	  	'<action:.*>'      => 'home/<action>',
	  	'brand.html' => 'home/site/brand',
	  	'brand-zone.html' => 'home/site/brand_zone',
	  	'cart.html' => 'home/simple/cart',
	  	'login.html' => 'home/simple/login',
	  	'help.html' => 'home/site/help',
	  	'help-index.html' => 'home/site/help_list',
	  	'notice.html' => 'home/site/notice',
	  	'tuan-list.html' => 'home/site/groupon_list',
	  	'search.html' => 'home/site/search_list',
	  	'error.html' => 'home/site/error',
	  	'notice-<id:\\d+>.html' => 'home/site/notice_detail',
	),
);
return $config;
?>
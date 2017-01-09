<html>
<head>
	<title>Error show</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="ROBOTS" content="NOINDEX,NOFOLLOW,NOARCHIVE" />
	<style type="text/css">
	body { background-color: white; color: black; font: 9pt/11pt verdana, arial, sans-serif;}
	#container { width: 1024px; margin:0px auto;margin-top:40px}
	#message   { width: 1024px; color: black; }
	a:link     { font: 9pt/11pt verdana, arial, sans-serif; color: red; }
	a:visited  { font: 9pt/11pt verdana, arial, sans-serif; color: #4e4e4e; }
	h1{ color: #FF0000; font: 14pt "Verdana"; margin-bottom: 0.5em;}
	.bg1{ background-color: #FFFFCC;}
	.bg2{ background-color: #EEEEEE;}
	.table {background: #AAAAAA; font:12px Menlo,Consolas,"Lucida Console"}
	.info {
		background: none repeat scroll 0 0 #F3F3F3;border: 0px solid #aaaaaa;color: #000000;
		font-size:12px;line-height:160%;margin-bottom:10px;padding:10px;
	}
	.info b{
		font-size:14px;
	}
	.help{
		display:block;width:100%;height:25px;line-height:25px;background-color:#eeeeee;text-indent:10px;
	}
	</style>
</head>
<body>
<div id="container">
<h1>Database Error</h1>
<div class='info'>
<?php if(self::$config['SHOW_ERROR_MSG'] && self::$config['DEBUG']):?>
	<strong>SQLState:</strong> <?php echo $error[0];?><br/>
	<strong>SQLError:</strong> <?php echo $error[1];?><br/>
	<strong>ErrorInfo:</strong> <?php echo $error[2];?><br/>
	<strong>SQL&nbsp;&nbsp;Info:</strong> <?php echo $sql;?>
<?php else:?>
<b>SQL Error:</b><?php echo $error[1];?>&#12288;<b>SQLState:</b><?php echo $error[0];?> (开启调试模式或者在错误日志里可以看到详细错误)
<?php endif;?>
</div>
<?php echo $traceTr;?> 
<div class="help">Simple And Easy OOP PHP Framework <a href="http://www.loadphp.com" target="_blank">LoadPHP</a> Ver0.1</div>
</div>
</body>
</html>
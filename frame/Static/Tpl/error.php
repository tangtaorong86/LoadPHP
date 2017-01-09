<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>消息提示</title>
<style type="text/css">
*{
	font-family: "Microsoft YaHei","微软雅黑","Lantinghei SC","Open Sans",Arial,"Hiragino Sans GB","STHeiti","WenQuanYi Micro Hei",SimSun,sans-serif;
}
.blank100{
	height:100px;
}
.text-center {
    margin: 0 auto;
    text-align: center;
    border-radius: 10px;
    max-width: 900px;
    -moz-box-shadow: 0px 0px 5px rgba(0,0,0,.3);
    -webkit-box-shadow: 0px 0px 5px rgba(0,0,0,.3);
    box-shadow: 0px 0px 5px rgba(0,0,0,.1);
    border: 1px solid #EEEEEE;
    padding-bottom: 50px;
}
h2 {
    padding-top: 20px;
    font-size: 20px;
}

.btn-primary{
	width:100px;
	text-align: center;
	text-decoration: none;
    color: #fff;
    background-color: #337ab7;
    border-color: #2e6da4;
}
.btn-primary:hover{
    background-color: #3399CC;
    border-color: #3399CC;
}
.btn {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
}
.error{
	color:red;
}
</style>
<div class="blank100"></div>
<div class="text-center">
  	<h2>
  		<?php if(isset($message)) {?>
		<p class="success"><?php echo($message); ?></p>
		<?php }else{?>
		<p class="error"><?php echo($error); ?></p>
		<?php }?>
  	</h2>
	<div class="padding-big">
		<a id="href" href="<?php echo($jumpUrl); ?>" class="btn btn-primary"><font id="wait"><?php echo($waitSecond); ?></font> 秒后即将跳转</a>
	</div>
</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</body>
</html>
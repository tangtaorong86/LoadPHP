<?php if(!defined('FRAME_VER')) exit;?><div style="height:auto;width:600px;margin-bottom:10px;border:1px solid red;">
<b>变量：</b><br>
<?php echo $aaa; ?>
<hr/>
<b>loop：</b><br/>
<?php $n=1; if(is_array($arr)) foreach($arr AS $k => $v) { ?>
<?php echo $v; ?>
<?php $n++;}unset($n); ?>
<hr>
<b>eval函数：</b><br>
<?php echo time();?>
<hr/>
<b>for：</b><br>
<?php for($a=0;$a<=10;$a++) { ?>
<?php echo $a; ?>
<?php } ?>
<hr/>
<b>if：</b><br>
<?php if($test == 2) { ?>
no
<?php } elseif ($test == 1) { ?>
yes
<?php } ?>
<hr/>
<b>include包含模板</b>  <br>

</div>
aaaaaaaaaaaaa
<?php
$config = array();
$config['ACCESSORY_SIZE']=12; //附件大小，单位M
$config['ACCESSORY_NUM']=10; //上传数量
$config['ACCESSORY_TYPE']='jpg,jpeg,bmp,gif,png,flv,mp4,mp3,wma,mp4,7z,zip,rar,ppt,txt,pdf,xls,doc,swf,wmv,avi,rmvb,rm';//上传格式
$config['PICTURE_TYPE']='jpg,jpeg,bmp,gif,png';//图片格式
$config['THUMBNAIL_SWITCH']=true; //是否缩图
$config['THUMBNAIL_MAXWIDTH']=200; //缩图最大宽度
$config['THUMBNAIL_MAXHEIGHT']=140; //最大高度
$config['WATERMARK_SWITCH']=false; //是否打水印
$config['WATERMARK_PLACE']=9; //水印位置
$config['WATERMARK_LOGO']='./public/watermark/watermark.png'; //水印图片

return $config;

?>
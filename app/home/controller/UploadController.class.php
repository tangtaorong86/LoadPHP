<?php
namespace app\home\controller;
use Core\Controller;
use Library\Upload;
class UploadController extends Controller{
	
	public function index(){
		if(IS_POST){
			$config = Config("attachment");
			$upload = new Upload();
			$upload_dir = "./uploads/";
				//设置上传文件大小
			$upload->maxSize = $config['ACCESSORY_SIZE']*1024*1024;//大小
			//设置上传文件类型
			$upload->allowExts = explode(',',$config['ACCESSORY_TYPE']);
			// 使用对上传图片进行缩略图处理
			$upload->thumb = $config['THUMBNAIL_SWITCH'];//是否缩略
			// 缩略图最大宽度
			$upload->thumbMaxWidth = $config['THUMBNAIL_MAXWIDTH'];
			// 缩略图最大高度
			$upload->thumbMaxHeight = $config['THUMBNAIL_MAXHEIGHT'];
	
			//设置附件上传目录
			$upload->savePath = $upload_dir;
			$upload->saveRule = 'uniqid_md5';
			if(!$upload->upload()){
				//捕获上传异常
				var_dump($upload->getErrorMsg());
			}else{
				//取得成功上传的文件信息
				var_dump($upload->getUploadFileInfo());
			}
		}else{
			$this->display();
		}
	}
	
	
}

?>
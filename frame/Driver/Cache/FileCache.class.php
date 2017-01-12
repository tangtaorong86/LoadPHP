<?php
/**
 * 文件级缓存类
 *
 * @package default
 * @copyright (c) http://www.loadphp.com All rights reserved.
 * @author  tangtaorong86
 */
namespace Driver\Cache;
use Driver\CacheInterface;
class FileCache implements CacheInterface{
	
	private $config = array();//缓存配置
	private $cachePath = ''; //默认文件缓存存放路径
	private $cacheExt = '.data';  //默认文件缓存扩展名
	private $directoryLevel = 1; //目录层级,基于$cachePath之下的

	/**
	 * 构造函数
	 */
	public function __construct(){
		$this->config = \Core\Factory::getConfig("cache");
		$this->cachePath = $this->config['DATA_CACHE_PATH'];
		$this->cacheExt  = $this->config['DATA_CACHE_EXT'];
	}

	/**
	 * 根据key值计算缓存文件名
	 * @param  string $key 缓存的唯一key值
	 * @return string 缓存文件路径
	 */
	private function getFileName($key){
		$key      = str_replace(' ','',$key);
		$cacheDir = rtrim($this->cachePath,'\\/').'/';
		if($this->directoryLevel > 0){
			$hash = abs(crc32($key));
			$cacheDir .= $hash % 1024;
			for($i = 1;$i < $this->directoryLevel;++$i){
				if(($prefix = substr($hash,$i,2)) !== false){
					$cacheDir .= '/'.$prefix;
				}
			}
		}
		return $cacheDir.'/'.md5($key).$this->cacheExt;
	}

	/**
	 * 写入缓存
	 * @param  string $key     缓存的唯一key值
	 * @param  mixed  $data    要写入的缓存数据
	 * @param  int    $expire  缓存数据失效时间,单位：秒
	 * @return bool   true:成功;false:失败;
	 */
	public function set($key,$data,$expire = ''){
		$fileName = $this->getFileName($key);
		if(!file_exists($dirname=dirname($fileName))){
			if(!file_exists($dirname)){
				if(!dmkdir($dirname,0755,TRUE)){
					throw new IException('缓存目录创建失败！请检查'.$this->path.'缓存的读写状态！');
				}			
			}
		}

		$writeLen = file_put_contents($fileName,$data);

		if($writeLen == 0){
			return false;
		}else{
			$expire = time() + $expire;
			touch($fileName,$expire);
			return true;
		}
	}

	/**
	 * 读取缓存
	 * @param  string $key 缓存的唯一key值,当要返回多个值时可以写成数组
	 * @return mixed  读取出的缓存数据;null:没有取到数据或者缓存已经过期了;
	 */
	public function get($key){
		$fileName = $this->getFileName($key);
		if(file_exists($fileName)){
			if(time() > filemtime($fileName)){
				$this->del($key,0);
				return null;
			}else{
				return file_get_contents($fileName);
			}
		}else{
			return null;
		}
	}

	/**
	 * 删除缓存
	 * @param  string $key     缓存的唯一key值
	 * @param  int    $timeout 在间隔单位时间内自动删除,单位：秒
	 * @return bool   true:成功; false:失败;
	 */
	public function del($key,$timeout = ''){
		$fileName = $this->getFileName($key);
		if(file_exists($fileName)){
			if($timeout > 0){
				$timeout = time() + $timeout;
				return touch($fileName,$timeout);
			}else{
				return unlink($fileName);
			}
		}else{
			return true;
		}
	}

	/**
	 * 删除全部缓存
	 * @return bool   true:成功；false:失败;
	 */
	public function flush(){
		$dir = $this->cachePath;
		if(!in_array($dir,self::$except) && is_dir($dir) && is_writable($dir)){
			$dirRes = opendir($dir);
			while($fileName = readdir($dirRes)){
				if(!in_array($fileName,self::$except)){
					$fullpath = $dir.'/'.$fileName;
					if(is_file($fullpath)){
						self::unlink($fullpath);
					}else{
						self::clearDir($fullpath);
						rmdir($fullpath);
					}
				}
			}
			closedir($dirRes);
			return true;
		}else{
			return false;
		}
	}
}
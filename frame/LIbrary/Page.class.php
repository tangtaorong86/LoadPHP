<?php
/**
 * 分页类
 *
 * @package Page
 */
namespace Library;
class Page {
	private $total;			//总数量
	private $limit;			//返回mysql的limit语句
	private $pageStart;		//开始的数值
	private $pageStop;		//结束的数值
	private $pageNumber;	//显示分页数字的数量
	private $page;			//当前页
	private $pageSize;		//每页显示的数量
	private $pageToatl;		//分页的总数量
	private $pageParam;		//分页变量
	private $uri;			//URL参数
	/**
	 * 分页设置样式 不区分大小写
	 * %pageToatl%  //总页数
	 * %page%		//当前页
	 * %pageSize% 	//当前页显示数据条数
	 * %pageStart%	//本页起始条数
	 * %pageStop%	//本页结束条数
	 * %total%		//总数据条数
	 * %first%		//首页
	 * %ending%		//尾页
	 * %up%			//上一页
	 * %down%		//下一页
	 * %F%			//起始页
	 * %E%			//结束页
	 * %omitFA%		//前省略加跳转
	 * %omitEA%		//后省略加跳转
	 * %omitF%		//前省略
	 * %omitE%		//后省略
	 * %numberF%	//固定数量的数字分页
	 * %numberD%	//左右对等的数字分页
	 * %input%		//跳转输入框
	 * %GoTo%			//跳转按钮
	 */
	private $pageType = '<span class="pageher">第%page%页/共%pageToatl%页</span>%first%%up%%F%%omitFA%%numberF%%omitEA%%E%%down%%ending%';
	//显示值设置
	private $pageShow = array('first'=>'首页','ending'=>'尾页','up'=>'上一页','down'=>'下一页','GoTo'=>'GO');

	/**
	 * 初始化数据,构造方法
	 * @access public
	 * @param int $total 		数据总数量
	 * @param int $pageSize 	每页显示条数
	 * @param int $pageNumber 	分页数字显示数量(使用%numberF%和使用%numberD%会有不同的效果)
	 * @param string $pageParam	分页变量
	 * @return void
	 */
	public function __construct($total,$pageSize=10,$pageNumber=5,$pageParam='p'){
		$this->total = $total<0?0:$total;
		$this->pageSize = $pageSize<0?0:$pageSize;
		$this->pageNumber = $pageNumber<0?0:$pageNumber;
		$this->pageParam = $pageParam;
		$this->calculate();
		$this->uri = __SYS_ENTRY__;
	}

	/**
	 * 显示分页
	 * @access public 
	 * @return string HTML分页字符串
	 */
	public function pageShow(){
		if($this->pageToatl>1){
			if($this->page == 1){
				$first = '<span class="disabled">'.$this->pageShow['first'].'</span>';
				$up = '<span class="disabled">'.$this->pageShow['up'].'</span>';
			}else{
				$first = '<a href="'.$this->uri.'&'.$this->pageParam.'=1">'.$this->pageShow['first'].'</a>';
				$up = '<a href="'.$this->uri.'&'.$this->pageParam.'='.($this->page-1).'">'.$this->pageShow['up'].'</a>';
			}
			if($this->page >= $this->pageToatl){
				$ending = '<span class="disabled">'.$this->pageShow['ending'].'</span>';
				$down = '<span class="disabled">'.$this->pageShow['down'].'</span>';
			}else{
				$ending = '<a href="'.$this->uri.'&'.$this->pageParam.'='.$this->pageToatl.'">'.$this->pageShow['ending'].'</a>';
				$down = '<a href="'.$this->uri.'&'.$this->pageParam.'='.($this->page+1).'">'.$this->pageShow['down'].'</a>';
			}
			$input  = '<input type="text" value="'.$this->page.'" onkeydown="javascript: if(event.keyCode==13){var value = parseInt(this.value); var page=(value>'.$this->pageToatl.') ? '.$this->pageToatl.' : value;  location=\''.$this->uri.'&'.$this->pageParam.'=\'+page+\'\'; return false;}">';
			$GoTo = '<button onclick="javascript:var value=parseInt(this.previousSibling.value); var page=(value>'.$this->pageToatl.') ? '.$this->pageToatl.' : value;  location=\''.$this->uri.'&'.$this->pageParam.'=\'+page+\'\'; return false;">'.$this->pageShow['GoTo'].'</button>';
		}else{
			$first = '';$up ='';$ending = '';$down = '';$input = '';$GoTo = '';
		}
		$this->numberF();
		return str_ireplace(array('%pageToatl%','%page%','%pageSize%','%pageStart%','%pageStop%','%total%','%first%','%ending%','%up%','%down%','%input%','%GoTo%'),array($this->pageToatl,$this->page,$this->pageStop-$this->pageStart,$this->pageStart,$this->pageStop,$this->total,$first,$ending,$up,$down,$input,$GoTo),$this->pageType);
	}

	
	/**
	 *	数字分页
	 */
	private function numberF(){
		$pageF = stripos($this->pageType,'%numberF%');
		$pageD = stripos($this->pageType,'%numberD%');
		$numberF = '';$numberD = '';$F = '';$E ='';$omitF = '';$omitFA = '';$omitE = '';$omitEA = '';
		if($pageF!==false || $pageD!==false){
			if($pageF!==false){
				$number = $this->pageNumber%2==0?$this->pageNumber/2:($this->pageNumber+1)/2;
				$DStart = $this->page - $number<0?$this->page - $number-1:0;
				if($this->page+$number-$DStart > $this->pageToatl){
					$DStop = ($this->page+$number-$DStart) - $this->pageToatl;
					$forStop = $this->pageToatl+1;
				}else{
					$DStop = 0;
					$forStop = $this->page+$number-$DStart+1;
				}
				$forStart = $this->page-$number-$DStop<1?1:$this->page-$number-$DStop;
				for($i=$forStart;$i<$forStop;++$i){
					if($i==$this->page){
						$numberF .= '<span class="current">'.$i.'</span>';
					}else{
						$numberF .= '<a href="'.$this->uri.'&'.$this->pageParam.'='.$i.'">'.$i.'</a>';
					}
				}
			}
			if($pageD!==false){
				$number = $this->pageNumber;
				$forStart = $this->page-$number>0?$this->page-$number:1;
				$forStop = $this->page+$number>$this->pageToatl?$this->pageToatl+1:$this->page+$number+1;
				for($i=$forStart;$i<$this->page;++$i){
					$numberD .= '<a href="'.$this->uri.'&'.$this->pageParam.'='.$i.'">'.$i.'</a>';
				}
				$numberD .= '<span class="current">'.$this->page.'</span>';
				$start = $this->page+1;
				for($i=$start;$i<$forStop;++$i){
					$numberD .= '<a href="'.$this->uri.'&'.$this->pageParam.'='.$i.'">'.$i.'</a>';
				}
			}
			$F = $forStart>1?'<a href="'.$this->uri.'&'.$this->pageParam.'=1">1</a>':'';
			$E = $forStop<$this->pageToatl+1?'<a href="'.$this->uri.'&'.$this->pageParam.'='.$this->pageToatl.'">'.$this->pageToatl.'</a>':'';
			if($forStart>2){
				$omitF = '<span  class="omit">…</span>';
				$startA = $this->page-$number<1?1:$this->page-$number;
				$omitFA = '<a  class="omit" href="'.$this->uri.'&'.$this->pageParam.'='.$startA.'">…</a>';
			}
			if($forStop<$this->pageToatl){
				$omitE = '<span class="omit">…</span>';
				$stopA = $this->page+$number>$this->pageToatl?$this->pageToatl:$this->page+$number;
				$omitEA = '<a  class="omit" href="'.$this->uri.'&'.$this->pageParam.'='.$stopA.'">…</a>';
			}
		}
		$this->pageType = str_ireplace(array('%F%','%E%','%omitFA%','%omitEA%','%omitF%','%omitE%','%numberF%','%numberD%'),array($F,$E,$omitFA,$omitEA,$omitF,$omitE,$numberF,$numberD),$this->pageType);
	}

	/**
	 *	处理url的方法
	 * @access public
	 * @param array   $url 	保持URL直对关系数组
	 * @return string 		过滤后的url尾参数
	 */
	private function uri(){
//		$url = $_SERVER["REQUEST_URI"];
		$url = \Core\Factory::getDriver(Config('app.SERVER_TYPE'),"ServerVars");
		
		$par = parse_url($url);
		if (isset($par['query'])) {
			parse_str($par['query'],$query);
			if(!is_array($this->uri)){
				$this->uri = array();
			}
			array_merge($query,$this->uri);
			unset($query[$this->pageParam]);
			while($key = array_search('',$query)){
				unset($query[$key]);
			}
			$this->uri = $par['path'].'?'.http_build_query($query);
		}else{
			$this->uri = $par['path'].'?';
		}
	}

	/**
	 * 设置limit方法及计算起始条数和结束条数
	 */
	private function calculate(){
		$this->pageToatl = ceil($this->total/$this->pageSize);
		$this->page = isset($_GET[$this->pageParam]) ? intval(@$_GET[$this->pageParam]) : 0;
		$this->page = $this->page>=1?$this->page>$this->pageToatl?$this->pageToatl:$this->page:1;
		$this->pageStart = ($this->page-1)*$this->pageSize;
		$this->pageStop = $this->pageStart+$this->pageSize;
		$this->pageStop = $this->pageStop>$this->total?$this->total:$this->pageStop;
		$this->limit = $this->pageStart.','.$this->pageStop;
	}

	/**
	 * 设置过滤器
	 */
	public function __set($name,$value){
		switch($name){
			case 'pageType':
			case 'uri':
				$this->$name = $value;
				return;
			case 'pageShow':
				if(is_array($value)){
					$this->pageShow = array_merge($this->pageShow,$value);
				}
				return;
		}
	}

	/**
	 * 取值过滤器
	 */
	public function __get($name){
		switch($name){
			case 'limit':
			case 'pageStart':
			case 'pageStop':
				return $this->$name;
			default:
				return;
		}
	}
	
}
<?php
/**
* 分页类
* @author LuckPHP
*
*/
namespace Luck\Tool;
class Page {
	private $total;				//总记录
	private $pagesize = 15;		//每页显示多少条
	private $limit;				//需返回limit(数据库查询使用)
	private $page;				//当前页码
	private $pagenum;			//总页码
	private $url;				//URL地址
	private $bothnum = 2;		//两边保持数字分页的量
	private $spacer = '?';		//URL间隔符
	
	//构造方法初始化
	public function __construct($_total, $_pagesize = '', $_spacer = '', $_bothnum = '') {
		$this->total = $_total ? $_total : 1;
		$this->pagesize = $_pagesize ? $_pagesize : $this->pagesize;
		$this->spacer = $_spacer ? $_spacer : $this->spacer;	
		$this->bothnum = $_bothnum ? $_bothnum : $this->bothnum;		
		$this->pagenum = ceil($this->total / $this->pagesize);
		$this->page = $this->setPage();
		$this->limit = ($this->page-1) * $this->pagesize.",$this->pagesize";
		$this->url = $this->setUrl();
	}
	
	//拦截器
	public function __get($_key) {
		return $this->$_key;
	}
	
	//获取当前页码
	private function setPage() {
		if(!empty($_GET['page'])) {
			if($_GET['page'] > 0) {
				if($_GET['page'] > $this->pagenum) {
					return $this->pagenum;
				} else {
					return $_GET['page'];
				}
			} else {
				return 1;
			}
		} else {
			return 1;
		}
	}	
	
	//处理URL
	private function setUrl() {
		$_url = $_SERVER["REQUEST_URI"];
		$_par = parse_url($_url);
		if (isset($_par['query'])) {
			parse_str($_par['query'],$_query);
			unset($_query['page']);
			$_url = $_par['path'].http_build_query($_query);
		} else {
			$patten = '/(\\'.$this->spacer.'page=[0-9]*)/';
			if(preg_match($patten, $_par['path'])) {
				$_par['path'] = preg_replace($patten, "", $_par['path']);
				$_url = $_par['path'];
			}
		}
		return $_url;
	}

	//数字目录
	private function pageList() {
		$_pagelist = null;
		for ($i=$this->bothnum; $i>=1; $i--) {
			$_page = $this->page - $i;
			if ($_page < 1) continue;
			$_pagelist .= ' <a href="'.$this->url.$this->spacer.'page='.$_page.'">'.$_page.'</a> ';
		}
		$_pagelist .= ' <span class="me">'.$this->page.'</span> ';
		for ($i=1;$i<=$this->bothnum;$i++) {
			$_page = $this->page+$i;
			if ($_page > $this->pagenum) break;
			$_pagelist .= ' <a href="'.$this->url.$this->spacer.'page='.$_page.'">'.$_page.'</a> ';
		}
		return $_pagelist;
	}
	
	//数据总量
	private function total() {
		return '<span class="total">共'.$this->total.'条数据</span>';
	}
	
	//首页
	private function first() {
		if ($this->page > $this->bothnum+1) {
			return ' <a href="'.$this->url.'">1</a> ...';
		}
	}
	
	//上一页
	private function prev() {
		if ($this->page == 1) {
			return '<span class="disabled">上一页</span>';
		}
		return ' <a href="'.$this->url.$this->spacer.'page='.($this->page-1).'">上一页</a> ';
	}
	
	//下一页
	private function next() {
		if ($this->page == $this->pagenum) {
			return '<span class="disabled">下一页</span>';
		}
		return ' <a href="'.$this->url.$this->spacer.'page='.($this->page+1).'">下一页</a> ';
	}
	
	//尾页
	private function last() {
		if ($this->pagenum - $this->page > $this->bothnum) {
			return ' ...<a href="'.$this->url.$this->spacer.'page='.$this->pagenum.'">'.$this->pagenum.'</a> ';
		}
	}
	
	//分页信息
	public function showpage() {
		if($this->pagesize >= $this->total) {
			return false;
		} else {
			$_page = null;
			$_page .= $this->prev();
			$_page .= $this->first();
			$_page .= $this->pageList();
			$_page .= $this->last();
			$_page .= $this->next();
			$_page .= $this->total();
			return $_page;
		}
	}
}

?>
<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}
class paginator {
	
	private $perpage;
	private $page;
	private $start;
	private $limit;
	private $multi;
	private $count;
	
	function __construct($count, $url = "") {
		global $_G;
		$this->count = $count;
		$this->perpage = getgpc('perpage');
		if(!empty($this->perpage)) {
			$_SESSION['perpage'] = $this->perpage;
		} else {
			$this->perpage = empty($_SESSION['perpage'])? $_G['setting']['perpage'] : $_SESSION['perpage'];
		}
		
		$this->page = empty($_GET['page'])?0:intval($_GET['page']);
		if($this->page<1) $this->page=1;
		$this->start = ($this->page-1)*$this->perpage;
		if(!$_G['mobile']) {
			$this->limit = "LIMIT $this->start, $this->perpage";
		}
		
		if($url == "") {
			$url = "index.php?home=".$_G['controller'];
		}
		$this->multi = multi($this->count, $this->perpage, $this->page, $url);
		
	}
	
	function get_perpage() {
		return '1';
		return $this->perpage;
	}
	function get_page() {
		return $this->page;
	}
	function get_start() {
		return $this->start;
	}
	function get_limit() {
		return $this->limit;
	}
	function get_count() {
		return $this->count;
	}
	function get_multi() {
		return $this->multi;
	}
	
}
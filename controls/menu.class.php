<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class menu_controller {

	//构造函数
	public function __construct() {
		include ROOT_PATH.'./models/common.php';
		$this->company = new common('company');
		$this->dishes = new common('dishes');
		$this->users = new common('users');
	}
	
	public function index_action() {
		global $_G;
		$company = $GLOBALS['db']->fetch_first("SELECT * FROM ".tname('company')." WHERE id='{$_G['company_id']}'");
		/*
		
		$filepatharr = explode(",", $company['filepath']);
		$patharr = explode("^", $filepatharr[0]);*/
		$category_list = $GLOBALS['db']->fetch_all("SELECT cid,name FROM ".tname('category')." WHERE uid='$company[uid]' ORDER BY displayorder ASC LIMIT 0,3");
		foreach($category_list as $k=>$v) {
			$category_list[$k]['count'] = $this->dishes->GetCount(" and uid='$company[uid]' AND cid='$v[cid]'");
		}
		//print_r($category_list);
		include template('menu');
	}
	
	
}

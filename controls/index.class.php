<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class index_controller {

	//构造函数
	public function __construct() {
		include_once ROOT_PATH.'./models/common.php';
		$this->company = new common('company');
		$this->dishes = new common('dishes');
		$this->users = new common('users');
	}
	
	public function index_action() {
		global $_G;
		$company = $GLOBALS['db']->fetch_first("SELECT * FROM ".tname('company')." WHERE id='{$_G['company_id']}'");
		$filepatharr = explode(",", $company['filepath']);
		$patharr = explode("^", $filepatharr[0]);
		$categoryArr = $GLOBALS['db']->fetch_all("SELECT name FROM ".tname('category')." WHERE uid='$company[uid]' ORDER BY displayorder ASC LIMIT 0,3");
		$main_menu = "";
		foreach($categoryArr as $v) {
			$main_menu .= ",".$v['name'];
		}
		$main_menu = substr($main_menu, 1);
		include_once template('index');
	}
	
	
}

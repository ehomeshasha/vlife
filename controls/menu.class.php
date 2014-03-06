<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class menu_controller {

	//构造函数
	public function __construct() {
		include_once ROOT_PATH.'./models/common.php';
		$this->company = new common('company');
		$this->dishes = new common('dishes');
		$this->users = new common('users');
	}
	
	public function index_action() {
		global $_G;
		foreach($_COOKIE as $k=>$v) {
			if(preg_match("/^food_count(\d+)$/", $k, $matches)) {
				$food_id = $matches[1];
				$foodArr[$food_id] = $v;
			}
		}
		$category_list = $GLOBALS['db']->fetch_all("SELECT cid,name FROM ".tname('category')." WHERE company_id='$_G[company_id]' ORDER BY displayorder ASC");
		$cid = getgpc('cid');
		if(empty($cid)) {
			$active_cid = $category_list[0]['cid'];
		} else {
			$active_cid = $cid;
		}
		foreach($category_list as $k=>$v) {
			$category_list[$k]['count'] = $this->dishes->GetCount(" and company_id='$_G[company_id]' AND cid='$v[cid]'");
		}
		$dishes = $GLOBALS['db']->fetch_all("SELECT * FROM ".tname('dishes')." WHERE company_id='$_G[company_id]' AND cid='$active_cid'");
		foreach($dishes as $key=>$val) {
			$filepatharr = explode(",", $val['filepath']);
			$patharr = explode("^", $filepatharr[0]);
			$dishes[$key]['path'] = $patharr[1];
			if(empty($foodArr[$val['id']])) {
				$dishes[$key]['current_count'] = 0;
			} else {
				$dishes[$key]['current_count'] = $foodArr[$val['id']];
			}
			 
		}
		
		
		
		
		include_once template('menu');
	}
	
	
}

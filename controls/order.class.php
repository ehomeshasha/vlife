<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class order_controller {

	//构造函数
	public function __construct() {
		include_once ROOT_PATH.'./models/common.php';
		$this->company = new common('company');
		$this->dishes = new common('dishes');
		$this->users = new common('users');
	}
	
	public function index_action() {
		global $_G;
		$orders = $GLOBALS['db']->fetch_all("SELECT * FROM ".tname('orders')." WHERE uid='$_G[uid]' ORDER BY dateline DESC");
		include_once template('order');
	}
	
	
}
?>
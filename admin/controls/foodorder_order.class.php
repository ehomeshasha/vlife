<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class foodorder_order_controller {

	public function __construct() {
		global $_G;
		include_once ROOT_PATH.'./models/common.php';
		$this->users = new common('users');
		$this->orders = new common('orders');
		if($_G['userlevel'] != $_G['setting']['userlevel']['company']) {
			$msg = "Company only for Admin Center";
			admin_login_page($msg);
		}
		check_company_exists();
	}
	
	public function index_action() {
		global $_G;
		$breadcrumb = array(
			array('text' => lang('order list')),
		);
		
		
		include_once ROOT_PATH.'./inc/paginator.class.php';
		$company_list = $GLOBALS['db']->fetch_all("SELECT id FROM ".tname('company')." WHERE uid='$_G[uid]' AND app='foodorder' ORDER BY dateline DESC $limit");
		$company_ids = "";
		foreach($company_list as $c) {
			$company_ids = ",'".$c['id']."'";
		}
		$company_ids = substr($company_ids, 1);
		$count = $this->orders->GetCount(" and company_id IN ($company_ids)");
		$paginator = new paginator($count);
		$perpage = $paginator->get_perpage();
		$limit = $paginator->get_limit();
		$multi = $paginator->get_multi();
		
		$orders = $GLOBALS['db']->fetch_all("SELECT * FROM ".tname('orders')." WHERE company_id IN ($company_ids) ORDER BY dateline DESC $limit");
		include_once template('admin#foodorder_order_list');
		
	}
	
	public function view_action() {
		global $_G;
		$id = getgpc('id');
		$order = $GLOBALS['db']->fetch_first("SELECT * FROM ".tname('orders')." WHERE id='$id'");
		$dishes = json_decode($order['dishes'], true);
				
		$breadcrumb = array(
			array('text' => lang('order list'), 'href' => 'index.php?home='.$_G['controller']),
			array('text' => lang('view')),
		);
		
		include_once template('admin#foodorder_order_view');
	}
	
}
?>
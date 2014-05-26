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
		
		$status = selectOpt(getgpc('status'), array($_G['setting']['order_status']['success'], $_G['setting']['order_status']['wait'], $_G['setting']['order_status']['failed']));
		$wait_count = $this->orders->GetCount(" and status='{$_G['setting']['order_status']['wait']}' {$_G['company_where']}");
		$success_count = $this->orders->GetCount(" and status='{$_G['setting']['order_status']['success']}' {$_G['company_where']}");
		$failed_count = $this->orders->GetCount(" and status='{$_G['setting']['order_status']['failed']}' {$_G['company_where']}");
		if($status == $_G['setting']['order_status']['wait']) {
			$wait_active = "active";
		} elseif($status == $_G['setting']['order_status']['success']) {
			$success_active = "active";
		} elseif($status == $_G['setting']['order_status']['failed']) {
			$failed_active = "active";
		}
		
		
		$breadcrumb = array(
			array('text' => lang('Order list')),
		);
		
		
		include_once ROOT_PATH.'./inc/paginator.class.php';
		$where = $_G['company_where']." AND status='$status'";
		$count = $this->orders->GetCount($where);
		$paginator = new paginator($count);
		$perpage = $paginator->get_perpage();
		$limit = $paginator->get_limit();
		$multi = $paginator->get_multi();
		
		$orders = $GLOBALS['db']->fetch_all("SELECT * FROM ".tname('orders')." WHERE 1 $where ORDER BY dateline DESC $limit");
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
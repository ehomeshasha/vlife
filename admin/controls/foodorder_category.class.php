<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class foodorder_category_controller {

	public function __construct() {
		global $_G;
		include_once ROOT_PATH.'./models/common.php';
		$this->users = new common('users');
		$this->category = new common('category');
		if($_G['userlevel'] != $_G['setting']['userlevel']['company']) {
			$msg = "Company only for Admin Center";
			login_page($msg);
		}
		check_company_exists();
	}
	
	
	public function post_action() {
		global $_G;
		$opt = selectOpt(getgpc('opt'), array('new','edit'));
		$displayorder = 0;
		if($_POST['submit'] != "true") {
			$head_text = lang('Create new category');
			
			if($opt == 'edit') {
				$cid = getgpc('cid');
				$category = $GLOBALS['db']->fetch_first("SELECT * FROM ".tname('category')." WHERE cid='$cid' AND uid='$_G[uid]'");
				$displayorder = $category['displayorder'];
				$head_text = lang('Edit category');
				$restaurant_html = init_restaurant($category['company_id']);
			} else {
				$restaurant_html = init_restaurant();
			}
			
			$category_html = init_category($_G['categorytree']['foodorder'], $category[fid], 0, 0);
			
			$breadcrumb = array(
				array('text' => lang('category list'), 'href' => 'index.php?home='.$_G['controller']),
				array('text' => $head_text),
			);
			$csrf = $GLOBALS['session']->get_csrf();
			
			
			include_once template('admin#foodorder_category_post');
			
			
		} else {
			
			$GLOBALS['session']->csrfguard_start();
			
			$name = chkLength("Restaurant name", getgpc('name'), 0, 30);
			$fid = chkDigits("Father category", getgpc('fid'), 1, 8);
			$company_id = chkDigits("Restaurant", getgpc('company_id'), 1, 8);
			$displayorder = chkDigits("Displayorder", getgpc('displayorder'), 1, 3);
			validate_start();
			
			$data = array(
				'name' => $name,
				'fid' => $fid,
				'company_id' => $company_id,
				'displayorder' => $displayorder,
				'app' => 'foodorder',
			);
			if($opt == 'new') {
				$msg = lang("Create new category successfully");
				$data['uid'] = $_G['uid'];
				$this->category->insert($data);
			} elseif($opt == 'edit') {
				$msg = lang("Edit category successfully");
				$cid = getgpc('cid');
				$this->category->UpdateData($data, " and cid='$cid'");
			}
			
			unlink(ROOT_PATH."data/cache/category.php");
			unlink(ROOT_PATH."data/cache/categorytree.php");
			
			
			$_SESSION['message'] = array('code' => '1', 'content' => array(lang($msg)));
			
			header('Location: index.php?home='.$_G['controller']);
			
			
			
			
			
			
			
			
		}
	
	}
	
	public function index_action() {
		global $_G;
		$breadcrumb = array(
			array('text' => lang('category list')),
			array(
				'text' => '+', 
				'href' => 'index.php?home='.$_G['controller'].'&act=post',
				'is_label' => 1, 
				'tooltip' => lang('Create new Category'),
				'label_type' => 'default',
			),
			
		);
		include_once template('admin#foodorder_category_list');
		
	}
	
	public function delete_action() {
		global $_G;
		$id = getgpc('id');
		$uid = getgpc('uid');
		if(empty($uid) || $uid == $_G['uid']) {
			$GLOBALS['db']->query("DELETE FROM ".tname('category')." WHERE `cid`='$id'");
			unlink(ROOT_PATH."data/cache/category.php");
			unlink(ROOT_PATH."data/cache/categorytree.php");
		}
	}
}
?>
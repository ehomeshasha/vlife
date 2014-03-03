<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class superadmin_controller {

	public function __construct() {
		global $_G;
		include ROOT_PATH.'./models/common.php';
		$this->users = new common('users');
		$superadmin_count = $this->users->GetCount(" and userlevel={$_G['setting']['userlevel']['superadmin']}");
		$this->userlevel_array = $_G['setting']['userlevel'];
		if($superadmin_count != 0) {
			unset($this->userlevel_array['superadmin']);	
		}
		
		if($_G['userlevel'] != $_G['setting']['userlevel']['superadmin'] && $superadmin_count != 0) {
			header("Location: index.php?home=login");
		}
	}
	
	public function index_action() {
		global $_G;
		
		$breadcrumb = array(
			$_G['BREAD_HOME'],
			array('text' => lang('User Management')),
			array(
				'text' => '+', 
				'href' => 'index.php?home=superadmin&act=post',
				'is_label' => 1, 
				'tooltip' => lang('Create new User'),
				'label_type' => 'default',
			),
			
		);
		
		$userlevel = selectOpt(getgpc('userlevel'), array($_G['setting']['userlevel']['company'], $_G['setting']['userlevel']['custom']));
		$custom_count = $this->users->GetCount(" and userlevel='{$_G['setting']['userlevel']['custom']}'");
		$company_count = $this->users->GetCount(" and userlevel='{$_G['setting']['userlevel']['company']}'");
		if($userlevel == $_G['setting']['userlevel']['custom']) {
			$custom_active = "active";
		} elseif($userlevel == $_G['setting']['userlevel']['company']) {
			$company_active = "active";
		}
		$perpage = getgpc('perpage');
		if(!empty($perpage)) {
			$_SESSION['perpage'] = $perpage;
		} else {
			$perpage = empty($_SESSION['perpage'])? $_G['setting']['perpage'] : $_SESSION['perpage'];
		}
		
		$page = empty($_GET['page'])?0:intval($_GET['page']);
		if($page<1) $page=1;
		$start = ($page-1)*$perpage;
		if(!$_G['mobile']) {
			$limit = "LIMIT $start, $perpage";
		}
		$user_list = $GLOBALS['db']->fetch_all("SELECT * FROM ".tname('users')." WHERE 1 AND userlevel='$userlevel' ORDER BY dateline DESC $limit");
		
		include template('admin_user');
	}
	
	public function post_action() {
		global $_G;
		$opt = selectOpt(getgpc('opt'), array('new','edit')); 
		$uid = getgpc('uid');
		
		if(!submitcheck('submit')) {
			$head_text = lang("Add User");
			$userlevel_array = $this->userlevel_array;
			
			
			
			$csrf = $GLOBALS['session']->get_csrf();
			if($opt == 'edit') {
				$head_text = lang("Edit User");
				$user = $GLOBALS['db']->fetch_first("SELECT username, userlevel FROM ".tname('users')." WHERE uid='$uid'");
			}
			
			$breadcrumb = array(
				$_G['BREAD_HOME'],
				array('text' => lang('User Management'), 'href' => 'index.php?home=superadmin'),
				array('text' => $head_text),
			);
			
			
			include template('user_post');
		} else {
			$GLOBALS['session']->csrfguard_start();
			$username = getgpc('username');
			$userlevel = getgpc('userlevel');
			$password = getgpc('password');
			if($username == "" || $password == "") {
				$_SESSION['message'] = array('code' => '-1', 'content' => array(lang("Please input username or password")));
				header('Location: index.php?home=superadmin&act=add');
			}
			
			if($opt == 'new' && getcount('users', "username='".global_addslashes($username)."'") > 0) {
				$_SESSION['message'] = array('code' => '-1', 'content' => array(lang("User exists")));
				header('Location: index.php?home=superadmin&act=add');
			}
			$data = array(
				'username' => $username,
				'password' => md5($password),
				'userlevel' => $userlevel,
			);
			if($opt == 'new') {
				$data['dateline'] = $_G['timestamp'];
				$this->users->insert($data);
				$msg = "Add User successfully";
			} else {
				$this->users->UpdateData($data, " and uid='$uid'");
				$msg = "You'v changed account information";
			}
			$_SESSION['message'] = array('code' => '1', 'content' => array(lang($msg)));
			
			header('Location: index.php?home=superadmin');
		}
		
		
		
	}
	
	
		
}
?>
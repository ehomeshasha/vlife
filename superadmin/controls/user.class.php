<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class user_controller {

	public function __construct() {
		global $_G;
		include_once ROOT_PATH.'./models/common.php';
		$this->users = new common('users');
		$superadmin_count = $this->users->GetCount(" and userlevel={$_G['setting']['userlevel']['superadmin']}");
		$this->userlevel_array = $_G['setting']['userlevel'];
		if($superadmin_count != 0) {
			unset($this->userlevel_array['superadmin']);	
		}
		
		if($_G['userlevel'] != $_G['setting']['userlevel']['superadmin'] && $superadmin_count != 0) {
			$msg = "Please login as superadmin first";
			superadmin_login_page($msg);
		}
	}
	
	public function index_action() {
		global $_G;
		
		$breadcrumb = array(
			array('text' => lang('User Management')),
			array(
				'text' => '+', 
				'href' => 'index.php?home='.$_G['controller'].'&act=post',
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
		
		include_once ROOT_PATH.'./inc/paginator.class.php';
		$count = $this->users->GetCount(" and userlevel='$userlevel'");
		$paginator = new paginator($count, "index.php?home=".$_G['controller']."&userlevel=$userlevel");
		$perpage = $paginator->get_perpage();
		$limit = $paginator->get_limit();
		$multi = $paginator->get_multi();
		
		$user_list = $GLOBALS['db']->fetch_all("SELECT * FROM ".tname('users')." WHERE 1 AND userlevel='$userlevel' ORDER BY dateline DESC $limit");
		
		include_once template('superadmin#user_list');
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
				$user = $GLOBALS['db']->fetch_first("SELECT uid, username, userlevel FROM ".tname('users')." WHERE uid='$uid'");
			}
			
			$breadcrumb = array(
				array('text' => lang('User Management'), 'href' => 'index.php?home='.$_G['controller']),
				array('text' => $head_text),
			);
			
			
			include_once template('superadmin#user_post');
		} else {
			$GLOBALS['session']->csrfguard_start();
			$username = getgpc('username');
			$userlevel = getgpc('userlevel');
			$password = getgpc('password');
			if($username == "" || $password == "") {
				$_SESSION['message'] = array('code' => '-1', 'content' => array(lang("Please input username or password")));
				header("Location: ".$_SERVER['HTTP_REFERER']);
			}
			
			if($opt == 'new' && getcount('users', "username='".global_addslashes($username)."'") > 0) {
				$_SESSION['message'] = array('code' => '-1', 'content' => array(lang("User exists")));
				header("Location: ".$_SERVER['HTTP_REFERER']);
			}
			$data = array(
				'username' => $username,
				'password' => md5($password),
				'userlevel' => $userlevel,
			);
			if($opt == 'new') {
				$data['dateline'] = $_G['timestamp'];
				$this->users->insert($data);
				$msg = lang("Add User successfully");
			} else {
				$this->users->UpdateData($data, " and uid='$uid'");
				$msg = lang("You'v changed account information");
			}
			$_SESSION['message'] = array('code' => '1', 'content' => array(lang($msg)));
			
			header('Location: index.php?home='.$_G['controller']);
		}
		
		
		
	}
	
	public function delete_action() {
		$id = getgpc('id');
		$GLOBALS['db']->query("DELETE FROM ".tname('users')." WHERE `uid`='$id'");
	}
		
}
?>
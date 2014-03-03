<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class company_controller {

	public function __construct() {
		global $_G;
		include ROOT_PATH.'./models/common.php';
		$this->users = new common('company');
	}
	
	
	
	public function post_action() {
		global $_G;
		
		if($_G['userlevel'] != in_array(array($_G['setting']['userlevel']['superadmin'], $_G['setting']['userlevel']['company']))) {
			$msg = "Userlevel is not correct";
			$_SESSION['message'] = array('code' => '-1', 'content' => array(lang($msg)));
			header("Location: index.php?home=login");
		}
		
		$opt = selectOpt(getgpc('opt'), array('new','edit')); 
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
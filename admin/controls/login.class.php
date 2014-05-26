<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class login_controller {
	public function __construct() {
		include_once ROOT_PATH.'./models/common.php';
		$this->users = new common('users');
	}
	public function index_action() {
		global $_G;
		$head_text = lang('Login Form');
		$breadcrumb = array(
			$_G['BREAD_HOME'],
			array('text' => $head_text),
		);
		$csrf = $GLOBALS['session']->get_csrf();
		include_once template('admin#admin_login');
	}
	public function login_action() {
		$GLOBALS['session']->csrfguard_start();
		
		$username = getgpc('username');
		$password = getgpc('password');
		
		$user = $this->users->GetOne(" AND username='$username'");
		if($user['password'] == md5($password)) {	//用户密码校验通过
			global $_G, $cookies;
			
			if($user['status'] == 0 || $user['status'] == -1) {
				$superadmin = $this->users->GetOne(" AND userlevel={$_G['setting']['userlevel']['superadmin']}", array("email"));
				if($user['status'] == 0) {	//商户还在待审核状态,无法登录管理后台
					$msg = lang("CompanyUser is waited for cetification by Superadmin, please connect $superadmin[email]");
				} elseif($user['status'] == -1) {	//商户处在无效状态,无法登录管理后台
					$msg = lang("CompanyUser is not valid yet, please connect $superadmin[email]");	
				}
				admin_login_page($msg);
			}
			
			$decode_str = urlencode($user['uid'])."&".urlencode($user['username'])."&".urlencode($user['password']);
			$encode_str = uc_authcode($decode_str, 'ENCODE', SYSTEM_KEY);
			$cookiearr = array(
				'admin_system_auth' => $encode_str
			);
			$cookies->set($cookiearr);
		} else {
			$msg = lang("Username or Password is not correct");
			admin_login_page($msg);
		}
		$next = getgpc('next');
		if(strpos($next, "index.php?home=login") === false) {
			header("Location: ".$next);
			
		} else {
			header("Location: index.php");	
		}
		
	}
	public function logout_action() {
		global $cookies;
		$cookiearr = array(
			'admin_system_auth' => ''
		);
		$cookies->destroy($cookiearr);
		header("Location: index.php");
	}
}
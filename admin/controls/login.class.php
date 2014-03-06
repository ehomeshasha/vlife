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
		include_once template('admin#login');
	}
	public function login_action() {
		$GLOBALS['session']->csrfguard_start();
		
		$username = getgpc('username');
		$password = getgpc('password');
		
		$user = $this->users->GetOne(" AND username='$username'");
		if($user['password'] == md5($password)) {
			global $_G, $cookies;
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
<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class setting_controller {

	//构造函数
	public function __construct() {
		include_once ROOT_PATH.'./models/common.php';
		$this->company = new common('company');
		$this->dishes = new common('dishes');
		$this->users = new common('users');
	}
	
	public function index_action() {
		global $_G;
		if($_POST['submit'] != "true") {
			$head_text = "Settings";
			$csrf = $GLOBALS['session']->get_csrf();
			include_once template('setting');
		} else {
			
			//$GLOBALS['session']->csrfguard_start();
			
			
			$contactname = chkLength("Contact name", getgpc('contactname'), 0, 30);
			$weixin = chkLength("WeiXin No.", getgpc('weixin'), 0, 20);
			$phone = chkLength("Telephone", getgpc('phone'), 0, 30);
			$address = chkLength("Your address", getgpc('address'), 0, 255);
			$password = trim($_POST['password']);
			validate_start();
			
			$user_data = array(
				'phone' => $phone,
				'address' => $address,
				'credits' => 0,
				'contactname' => $contactname,
				'weixin' => $weixin,
				'password' => $password,
			);
			echo '<pre>';
			print_r($user_data);
			user_save($user_data);
			
			//header("Location: index.php?home=".$_G['controller']);
			
			
		}
	}
	
	
}

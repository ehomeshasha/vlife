<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class foodorder_company_controller {

	public function __construct() {
		global $_G;
		include ROOT_PATH.'./models/common.php';
		$this->users = new common('users');
		$this->company = new common('company');
		if($_G['userlevel'] != $_G['setting']['userlevel']['company']) {
			$msg = "Company only for Admin Center";
			$_SESSION['message'] = array('code' => '-1', 'content' => array(lang($msg)));
			login_page();
		}
	}
	
	public function index_action() {
		
		global $_G;
		$where = "uid='$_G[uid]' AND app='foodorder'";
		$restaurant = $GLOBALS['db']->fetch_first("SELECT * FROM ".tname('company')." WHERE $where");
		if(!submitcheck('submit')) {
			$head_text = lang('Restaurant Information');
			$breadcrumb = array(
				$_G['ADMIN_BREAD_HOME'],
				array('text' => $head_text, 'href' => 'index.php?home='.$_G['controller']),
			);
			$csrf = $GLOBALS['session']->get_csrf();
			
			
			include template('admin#foodorder_company');
		} else {
			$GLOBALS['session']->csrfguard_start();
			$name = getgpc('name');
			$brand = getgpc('brand');
			$thumb = getgpc('file_upload');
			$description = getgpc('description');
			$phone = getgpc('phone');
			$address = getgpc('address');
			$data = array(
				'name' => $name,
				'brand' => $brand,
				'thumb' => $thumb,
				'description' => $description,
				'phone' => $phone,
				'address' => $address,
				'app' => 'foodorder',
			);
			if(empty($restaurant)) {
				$this->company->insert($data);
			} else {
				$this->company->UpdateData($data, " and $where");
			}
			
			
			$msg = lang("Setting your restaurant information successfully");
			$_SESSION['message'] = array('code' => '1', 'content' => array(lang($msg)));
			
			header('Location: index.php?home='.$_G['controller']);
		}
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
				$_G['SUPER_BREAD_HOME'],
				array('text' => lang('User Management'), 'href' => 'index.php?home='.$_G['controller']),
				array('text' => $head_text),
			);
			
			
			include template('superadmin#user_post');
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
				$msg = "Add User successfully";
			} else {
				$this->users->UpdateData($data, " and uid='$uid'");
				$msg = "You'v changed account information";
			}
			$_SESSION['message'] = array('code' => '1', 'content' => array(lang($msg)));
			
			header('Location: index.php?home='.$_G['controller']);
		}
		
		
		
	}
	
	
		
}
?>
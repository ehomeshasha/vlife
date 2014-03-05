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
			login_page($msg);
		}
	}
	
	public function index_action() {
		
		global $_G;
		$where = "uid='$_G[uid]' AND app='foodorder'";
		$restaurant = $GLOBALS['db']->fetch_first("SELECT * FROM ".tname('company')." WHERE $where");
		if(!submitcheck('submit')) {
			$head_text = lang('Restaurant Information');
			$breadcrumb = array(
				array('text' => $head_text, 'href' => 'index.php?home='.$_G['controller']),
			);
			$csrf = $GLOBALS['session']->get_csrf();
			$filepatharr = explode(",", $restaurant['filepath']);
			include template('admin#foodorder_company');
		} else {
			$GLOBALS['session']->csrfguard_start();
			
			$name = chkLength("Restaurant name", getgpc('name'), 0, 30);
			$brand = getgpc('brand');
			$phone = chkLength("TelePhone", getgpc('phone'), 0, 30);
			$address = chkLength("Restaurant address", getgpc('address'), 0, 100);
			$description = chkLength("Restaurant short description", getgpc('description'), 0, 255);
			$filepath = chkUploadExist("Restaurant thumb", getgpc('filepath'));
			validate_start();
			
			$data = array(
				'name' => $name,
				'brand' => $brand,
				'filepath' => $filepath,
				'description' => $description,
				'phone' => $phone,
				'address' => $address,
				'app' => 'foodorder',
			);
			if(empty($restaurant)) {
				$data['uid'] = $_G['uid'];
				$this->company->insert($data);
			} else {
				$this->company->UpdateData($data, " and $where");
			}
			
			
			$msg = lang("Setting your restaurant information successfully");
			$_SESSION['message'] = array('code' => '1', 'content' => array(lang($msg)));
			
			header('Location: index.php?home='.$_G['controller']);
		}
	}
}
?>
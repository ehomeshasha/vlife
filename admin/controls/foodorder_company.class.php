<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class foodorder_company_controller {

	public function __construct() {
		global $_G;
		include_once ROOT_PATH.'./models/common.php';
		$this->users = new common('users');
		$this->company = new common('company');
		if($_G['userlevel'] != $_G['setting']['userlevel']['company']) {
			$msg = "Company only for Admin Center";
			admin_login_page($msg);
		}
	}
	
	public function post_action() {
		
		global $_G;
		$opt = selectOpt(getgpc('opt'), array('new','edit'));
		if($_POST['submit'] != "true") {
			$head_text = lang('Create new Restaurant');
			
			if($opt == 'edit') {
				$id = getgpc('id');
				$restaurant = $GLOBALS['db']->fetch_first("SELECT * FROM ".tname('company')." WHERE id='$id' AND uid='$_G[uid]' AND app='foodorder'");
				$head_text = lang('Edit Restaurant Info.');
				$filepatharr = explode(",", $restaurant['filepath']);
			}
			
			
			//init_dish_options($_G['uid'], )
			
			$breadcrumb = array(
				array('text' => lang('restaurant list'), 'href' => 'index.php?home='.$_G['controller']),
				array('text' => $head_text),
			);
			$csrf = $GLOBALS['session']->get_csrf();
			include_once template('admin#foodorder_company_post');
		} else {
			
			$GLOBALS['session']->csrfguard_start();
			
			$name = chkLength("Restaurant name", getgpc('name'), 0, 30);
			$brand = getgpc('brand');
			$phone = chkLength("TelePhone", getgpc('phone'), 0, 30);
			$address = chkLength("Restaurant address", getgpc('address'), 0, 100);
			$description = chkLength("Restaurant short description", getgpc('description'), 0, 255);
			$filepath = chkUploadExist("Restaurant thumb", getgpc('filepath'));
			$weixin = chkLength("Weixin number", getgpc('weixin'), 0, 20);
			validate_start();
			
			$data = array(
				'name' => $name,
				'brand' => $brand,
				'filepath' => $filepath,
				'description' => $description,
				'phone' => $phone,
				'address' => $address,
				'weixin' => $weixin,
				'app' => 'foodorder',
			);
			if($opt == 'new') {
				$msg = lang("Create restaurant successfully");
				$data['uid'] = $_G['uid'];
				$data['dateline'] = $_G['timestamp'];
				$this->company->insert($data);
			} elseif($opt == 'edit') {
				$msg = lang("Setting your restaurant information successfully");
				$id = getgpc('id');
				$this->company->UpdateData($data, " and id='$id' AND uid='$_G[uid]' AND app='foodorder'");
			}
			$_SESSION['message'] = array('code' => '1', 'content' => array(lang($msg)));
			
			header('Location: index.php?home='.$_G['controller']);
		}
	}
	
	public function index_action() {
		global $_G;
		$breadcrumb = array(
			array('text' => lang('restaurant list')),
			array(
				'text' => '+', 
				'href' => 'index.php?home='.$_G['controller'].'&act=post',
				'is_label' => 1, 
				'tooltip' => lang('Create new Restaurant'),
				'label_type' => 'default',
			),
			
		);
		
		include_once template('admin#foodorder_company_list');
		
	}
	
	public function delete_action() {
		global $_G;
		$id = getgpc('id');
		$uid = getgpc('uid');
		if(empty($uid) || $uid == $_G['uid']) {
			$GLOBALS['db']->query("DELETE FROM ".tname('company')." WHERE `id`='$id'");
		}
	}
}
?>
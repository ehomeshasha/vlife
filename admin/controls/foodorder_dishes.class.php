<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class foodorder_dishes_controller {

	public function __construct() {
		global $_G;
		include_once ROOT_PATH.'./models/common.php';
		$this->users = new common('users');
		$this->dishes = new common('dishes');
		if($_G['userlevel'] != $_G['setting']['userlevel']['company']) {
			$msg = "Company only for Admin Center";
			admin_login_page($msg);
		}
		check_company_exists();
	}
	
	
	public function post_action() {
		global $_G;
		$opt = selectOpt(getgpc('opt'), array('new','edit'));
		$displayorder = 0;
		if($_POST['submit'] != "true") {
			$head_text = lang('Create new dish');
			
			if($opt == 'edit') {
				$id = getgpc('id');
				$dish = $GLOBALS['db']->fetch_first("SELECT * FROM ".tname('dishes')." WHERE id='$id'");
				$displayorder = $dish['displayorder'];
				$head_text = lang('Edit dish');
				$category_html = init_category($_G['categorytree']['foodorder'], $dish['cid']);
				$filepatharr = explode(",", $dish['filepath']);
				$restaurant_html = init_restaurant($dish['company_id']);
			} else {
				$category_html = init_category($_G['categorytree']['foodorder']);
				$restaurant_html = init_restaurant();
			}
			
			$breadcrumb = array(
				array('text' => lang('dishes'), 'href' => 'index.php?home='.$_G['controller']),
				array('text' => $head_text),
			);
			$csrf = $GLOBALS['session']->get_csrf();
			
			include_once template('admin#foodorder_dishes_post');
			
			
		} else {
			
			$GLOBALS['session']->csrfguard_start();
			$name = chkLength("Dish name", getgpc('name'), 0, 30);
			$company_id = chkDigits("Restaurant", getgpc('company_id'), 1, 8);
			$description = chkLength("Short description", getgpc('name'), -1, 255);
			$price = chkNumber("Price", getgpc('price'));
			$cid = chkDigits("Category", getgpc('cid'), 1, 8);
			$displayorder = chkDigits("Displayorder", getgpc('displayorder'), 1, 3);
			$filepath = chkUploadExist("Restaurant thumb", getgpc('filepath'));
			validate_start();
			
			$data = array(
				'name' => $name,
				'company_id' => $company_id,
				'description' => $description,
				'price' => $price,
				'cid' => $cid,
				'displayorder' => $displayorder,
				'filepath' => $filepath,
				'updatetime' => $_G['timestamp'],
			);
			
			if($opt == 'new') {
				$msg = lang("Create new dish successfully");
				$data['uid'] = $_G['uid'];
				$data['createtime'] = $_G['timestamp'];
				$this->dishes->insert($data);
			} elseif($opt == 'edit') {
				$msg = lang("Edit dish successfully");
				$id = getgpc('id');
				$this->dishes->UpdateData($data, " and id='$id'");
			}
			
			
			$_SESSION['message'] = array('code' => '1', 'content' => array(lang($msg)));
			
			header('Location: index.php?home='.$_G['controller']);
			
			
			
			
			
			
			
			
		}
	
	}
	
	public function index_action() {
		global $_G;
		$breadcrumb = array(
			array('text' => lang('dishes')),
			array(
				'text' => '+', 
				'href' => 'index.php?home='.$_G['controller'].'&act=post',
				'is_label' => 1, 
				'tooltip' => lang('Create new Dish'),
				'label_type' => 'default',
			),
			
		);
		
		include_once ROOT_PATH.'./inc/paginator.class.php';
		$count = $this->dishes->GetCount(" and uid='$_G[uid]'");
		$paginator = new paginator($count);
		$perpage = $paginator->get_perpage();
		$limit = $paginator->get_limit();
		$multi = $paginator->get_multi();
		
		$dishes = $GLOBALS['db']->fetch_all("SELECT a.*,b.name as restaurant_name 
		FROM ".tname('dishes')." AS a LEFT JOIN ".tname('company')." AS b ON a.company_id=b.id 
		WHERE a.uid='$_G[uid]' ORDER BY a.createtime DESC $limit");
		
		include_once template('admin#foodorder_dishes_list');
		
	}
	
	public function delete_action() {
		global $_G;
		$id = getgpc('id');
		$uid = getgpc('uid');
		if(empty($uid) || $uid == $_G['uid']) {
			$GLOBALS['db']->query("DELETE FROM ".tname('dishes')." WHERE `id`='$id'");
		}
	}
}
?>
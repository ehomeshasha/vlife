<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class foodorder_dishes_controller {

	public function __construct() {
		global $_G;
		include ROOT_PATH.'./models/common.php';
		$this->users = new common('users');
		$this->dishes = new common('dishes');
		if($_G['userlevel'] != $_G['setting']['userlevel']['company']) {
			$msg = "Company only for Admin Center";
			login_page($msg);
		}
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
			} else {
				$category_html = init_category($_G['categorytree']['foodorder']);
			}
			
			$breadcrumb = array(
				array('text' => lang('dishes'), 'href' => 'index.php?home='.$_G['controller']),
				array('text' => $head_text),
			);
			$csrf = $GLOBALS['session']->get_csrf();
			
			include template('admin#foodorder_dishes_post');
			
			
		} else {
			
			$GLOBALS['session']->csrfguard_start();
			$name = chkLength("Dish name", getgpc('name'), 0, 30);
			$description = chkLength("Short description", getgpc('name'), -1, 255);
			$price = chkNumber("Price", getgpc('price'));
			$cid = chkDigits("Category", getgpc('cid'), 1, 8);
			$displayorder = chkDigits("Displayorder", getgpc('displayorder'), 1, 3);
			$filepath = chkUploadExist("Restaurant thumb", getgpc('filepath'));
			validate_start();
			
			$data = array(
				'name' => $name,
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
		$dishes = $GLOBALS['db']->fetch_all("SELECT * FROM ".tname('dishes')." WHERE uid='$_G[uid]' ORDER BY createtime DESC $limit");
		
		
		include template('admin#foodorder_dishes_list');
		
	}
	
	public function delete_action() {
		$id = getgpc('id');
		$GLOBALS['db']->query("DELETE FROM ".tname('dishes')." WHERE `id`='$id'");
	}
}
?>
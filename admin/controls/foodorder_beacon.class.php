<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class foodorder_beacon_controller {

	public function __construct() {
		global $_G;
		include_once ROOT_PATH.'./models/common.php';
		$this->users = new common('users');
		$this->beacon = new common('beacon');
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
			$head_text = lang('Create new Beacon');
			
			if($opt == 'edit') {
				$id = getgpc('id');
				$beacon = $GLOBALS['db']->fetch_first("SELECT * FROM ".tname('beacon')." WHERE id='$id' AND uid='$_G[uid]'");
				$head_text = lang('Edit Beacon Info.');
				$dishes_option = init_dishes_option($beacon['dish_ids'], $beacon['company_id']);
				
			}
			
			$breadcrumb = array(
				array('text' => lang('Beacon list'), 'href' => 'index.php?home='.$_G['controller']),
				array('text' => $head_text),
			);
			$csrf = $GLOBALS['session']->get_csrf();
			
			
			include_once template('admin#foodorder_beacon_post');
			
			
		} else {
			//exit;
			$GLOBALS['session']->csrfguard_start();
			
			$uuid = chkLength("UUID", getgpc('uuid'), 0, 255);
			//check whether uuid is duplicated
			if($opt == 'new' && getcount('beacon', "uuid='".$uuid."'") > 0) {
				$msg = lang("UUID is duplicated");
				$_SESSION['message'] = array('code' => '-1', 'content' => array(lang($msg)));
				header('Location: index.php?home='.$_G['controller']);
				exit;
			}
			
			$company_id = chkLength("Restaurant", getgpc('company_id'), 0, 8);
			$name = chkLength("IBeacon name", getgpc('name'), 0, 100);
			$dish_idArray = getgpc('dishes');
			$dish_ids = implode(",", $dish_idArray);
			$url = chkLength("url", getgpc('url'), -1, 500);
			
			validate_start();
			
			$data = array(
				'name' => $name,
				'uuid' => $uuid,
				'dish_ids' => $dish_ids,
				'app' => 'foodorder',
				'company_id' => $company_id,
				'url' => $url,
			);
			if($opt == 'new') {
				$msg = lang("Create new beacon successfully");
				$data['uid'] = $_G['uid'];
				$data['dateline'] = $_G['timestamp'];
				$this->beacon->insert($data);
			} elseif($opt == 'edit') {
				$msg = lang("Edit beacon information successfully");
				$id = getgpc('id');
				$this->beacon->UpdateData($data, " and id='$id'");
			}
			
			$_SESSION['message'] = array('code' => '1', 'content' => array(lang($msg)));
			
			header('Location: index.php?home='.$_G['controller']);
		}
	}
	
	public function index_action() {
		global $_G;
		$breadcrumb = array(
			array('text' => lang('beacon list')),
			array(
				'text' => '+', 
				'href' => 'index.php?home='.$_G['controller'].'&act=post',
				'is_label' => 1, 
				'tooltip' => lang('Create new Beacon'),
				'label_type' => 'default',
			),
			
		);
		
		include_once ROOT_PATH.'./inc/paginator.class.php';
		$count = $this->beacon->GetCount(" AND 1=1");
		$paginator = new paginator($count);
		$perpage = $paginator->get_perpage();
		$limit = $paginator->get_limit();
		$multi = $paginator->get_multi();
		
		$beacon_list = $GLOBALS['db']->fetch_all("SELECT a.*,b.name AS company_name FROM ".tname('beacon')." AS a LEFT JOIN ".tname('company')." AS b ON a.company_id=b.id WHERE 1 ORDER BY dateline DESC $limit");
		
		
		include_once template('admin#foodorder_beacon_list');
		
	}
	
	
	public function view_action() {
		global $_G;
		
		$head_text = lang('View Beacon Info.');
		$breadcrumb = array(
			array('text' => lang('Beacon list'), 'href' => 'index.php?home='.$_G['controller']),
			array('text' => $head_text),
		);
		
		$id = getgpc('id');
		$beacon = $GLOBALS['db']->fetch_first("SELECT * FROM ".tname('beacon')." WHERE id='$id'");
		foreach($_G['company_list'] as $c) {
			if($c['id'] == $beacon['company_id']) {
				$company_name = $c['name'];
				break;
			}
		}
		$dish_ids = dimplode(explode(",", $beacon['dish_ids']));
		$dish_list = $GLOBALS['db']->fetch_all("SELECT * FROM ".tname('dishes')." WHERE id IN ($dish_ids)");
		foreach($dish_list as $k=>$d) {
			$filepath_arr = explode("^", $d['filepath']);
			$dish_list[$k]['alt'] = $filepath_arr[0];
			$dish_list[$k]['path'] = $filepath_arr[1];
		}
		
		include_once template('admin#foodorder_beacon_view');
	}
	
	public function delete_action() {
		global $_G;
		$id = getgpc('id');
		$uid = getgpc('uid');
		if(empty($uid) || $uid == $_G['uid']) {
			$GLOBALS['db']->query("DELETE FROM ".tname('beacon')." WHERE `id`='$id'");
		}
	}
}
?>
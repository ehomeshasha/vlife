<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class user_controller {

	public function __construct() {
		global $_G;
		include_once ROOT_PATH.'./models/common.php';
		$this->users = new common('users');
		$superadmin_count = $this->users->GetCount(" and userlevel={$_G['setting']['userlevel']['superadmin']}");
		$this->userlevel_array = $_G['setting']['userlevel'];
		if($superadmin_count != 0) {
			unset($this->userlevel_array['superadmin']);	
		}
		
		if($_G['userlevel'] != $_G['setting']['userlevel']['superadmin'] && $superadmin_count != 0) {
			$msg = "Please login as superadmin first";
			superadmin_login_page($msg);
		}
	}
	
	public function index_action() {
		global $_G;
		
		$company_where = " AND userlevel='{$_G['setting']['userlevel']['company']}'"; 
		$breadcrumb = array(
			array('text' => lang('CompanyUser List')),
			array(
				'text' => '+', 
				'href' => 'index.php?home='.$_G['controller'].'&act=post',
				'is_label' => 1, 
				'tooltip' => lang('Register new CompanyUser'),
				'label_type' => 'default',
			),
			
		);
		
		$status = selectOpt(getgpc('status'), array($_G['setting']['status']['accept'], $_G['setting']['status']['wait'], $_G['setting']['status']['reject']));
		$wait_count = $this->users->GetCount(" and status='{$_G['setting']['status']['wait']}' {$company_where}");
		$accept_count = $this->users->GetCount(" and status='{$_G['setting']['status']['accept']}' {$company_where}");
		$reject_count = $this->users->GetCount(" and status='{$_G['setting']['status']['reject']}' {$company_where}");
		if($status == $_G['setting']['status']['wait']) {
			$wait_active = "active";
		} elseif($status == $_G['setting']['status']['accept']) {
			$accept_active = "active";
		} elseif($status == $_G['setting']['status']['reject']) {
			$reject_active = "active";
		}
		
		include_once ROOT_PATH.'./inc/paginator.class.php';
		$count = $this->users->GetCount(" and status='$status' {$company_where}");
		$paginator = new paginator($count, "index.php?home=".$_G['controller']."&status=$status");
		$perpage = $paginator->get_perpage();
		$limit = $paginator->get_limit();
		$multi = $paginator->get_multi();
		
		$user_list = $GLOBALS['db']->fetch_all("SELECT * FROM ".tname('users')." WHERE 1 AND status='$status' {$company_where} ORDER BY dateline DESC $limit");
		
		include_once template('superadmin#user_list');
	}
	
	public function post_action() {
		global $_G;
		$opt = selectOpt(getgpc('opt'), array('new','edit')); 
		$uid = getgpc('uid');
		
		if(!submitcheck('submit')) {
			$head_text = lang("Add User");
			
			
			
			$csrf = $GLOBALS['session']->get_csrf();
			if($opt == 'edit') {
				$head_text = lang("Edit User");
				$user = $GLOBALS['db']->fetch_first("SELECT * FROM ".tname('users')." WHERE uid='$uid'");
			}
			
			$breadcrumb = array(
				array('text' => lang('CompanyUser Management'), 'href' => 'index.php?home='.$_G['controller']),
				array('text' => $head_text),
			);
			
			
			include_once template('superadmin#user_post');
		} else {
			$GLOBALS['session']->csrfguard_start();
			$username = getgpc('username');
			$email = getgpc('email');
			$status = getgpc('status');
			$password = getgpc('password');
			$msg_content = array();
			$validate = true;
			if(empty($username)) {
				$msg_content[] = lang("Please input username");
				$_SESSION['message'] = array('code' => '-1', 'content' => $msg_content);
				$validate = false;
			}
			
			if($opt == 'new' && getcount('users', "username='".$username."'") > 0) {
				$msg_content[] = lang("User exists");
				$_SESSION['message'] = array('code' => '-1', 'content' => $msg_content);
				$validate = false;
			}
			if($opt == 'new' && empty($password)) {
				$msg_content[] = lang("Please input password");
				$_SESSION['message'] = array('code' => '-1', 'content' => $msg_content);
				$validate = false;
			}
			if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$msg_content[] = lang("Invalid Email Address");
				$_SESSION['message'] = array('code' => '-1', 'content' => $msg_content);
				$validate = false;
			}
			if($opt == 'new' && getcount('users', "email='".$email."'") > 0) {
				$msg_content[] = lang("Email exists");
				$_SESSION['message'] = array('code' => '-1', 'content' => $msg_content);
				$validate = false;
			}
			
			if($validate === false) {
				header("Location: ".$_SERVER['HTTP_REFERER']);
				exit;
			}
			$data = array(
				'username' => $username,
				'email' => $email,
				'status' => $status,
			);
			if(!empty($password)) {
				$data['password'] = md5($password);
			}
			if($opt == 'new') {
				$data['dateline'] = $_G['timestamp'];
				$this->users->insert($data);
				$msg = lang("Add User successfully");
			} else {
				$this->users->UpdateData($data, " and uid='$uid'");
				$msg = lang("You'v changed account information");
			}
			$_SESSION['message'] = array('code' => '1', 'content' => array(lang($msg)));
			
			header('Location: index.php?home='.$_G['controller']);
		}
		
		
		
	}
	
	public function delete_action() {
		$id = getgpc('id');
		$GLOBALS['db']->query("DELETE FROM ".tname('users')." WHERE `uid`='$id'");
	}
		
}
?>
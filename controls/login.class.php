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
		include_once template('login');
	}
	public function login_action($simulate_login=0) {
		if($simulate_login == 0) {
			$GLOBALS['session']->csrfguard_start();
		}
		$username = getgpc('username');
		$password = getgpc('password');
		
		$user = $this->users->GetOne(" AND username='$username'");
		if($user['password'] == md5($password)) {
			global $_G, $cookies;
			$decode_str = urlencode($user['uid'])."&".urlencode($user['username'])."&".urlencode($user['password']);
			$encode_str = uc_authcode($decode_str, 'ENCODE', SYSTEM_KEY);
			$cookiearr = array(
				'system_auth' => $encode_str
			);
			$cookies->set($cookiearr);
		} else {
			$msg = lang("Username or Password is not correct");
			login_page($msg);
		}
		if($simulate_login == 0) {
			$next = getgpc('next');
			if(strpos($next, "index.php?home=login") === false) {
				header("Location: ".$next);
				
			} else {
				header("Location: index.php");	
			}	
		}
		
	}
	public function logout_action() {
		global $cookies;
		$cookiearr = array(
			'system_auth' => ''
		);
		$cookies->destroy($cookiearr);
		header("Location: index.php");
	}
	/*
	public function adduser_action() {
		global $_G;
		if($_G['userlevel'] != 9) exit('Access Denied');
		include template('adduser');
	}
	public function register_action() {
		global $_G;
		if($_G['userlevel'] != 9) showmessage('Access Denied');
		$username = getgpc('username');
		$password = getgpc('password');
		$userlevel = getgpc('userlevel');
		if($username == "" || $password == "") showmessage('请输入用户名和密码');
		if(getcount('users', "username='".global_addslashes($username)."'") > 0) showmessage('该用户名已存在');
		$data = array(
			'username' => $username,
			'password' => md5($password),
			'userlevel' => $userlevel,
			'dateline' => $_G['timestamp'],
		);
		$this->users->insert($data);
		$userlist = $this->users->getAll(' AND uid !=60 AND userlevel !=-1', array('uid','username','userlevel','manager','PM','dateline'), "ORDER BY userlevel DESC");
		write('userlist', $userlist);
		showmessage('已经成功添加用户', 1);
	}
	public function editpassword_action() {
		global $_G;
		if(!submitcheck('submit')) {
			$body .= "<label>新密码</label><input type='password' name='newpassword' id='newpassword' />
			<label>再次输入新密码</label><input type='password' id='repeat_newpassword' />";
			$submit_javascript = <<<EOF
<script type="text/javascript">
$(function(){
	$("#modal_form").submit(function(){
		var newpassword = $("#newpassword").val();
		var repeat_newpassword = $("#repeat_newpassword").val();
		if(newpassword == '') {
			alert('输入的密码不能为空');
			location.href = location.href;
			return false;
		}
		if(newpassword != repeat_newpassword) {
			alert('两次输入的密码不一致');
			location.href = location.href;
			return false;
		}
	});
});
</script>
EOF;
			$cfm_box = array(
				'input' => array('uid' => $_G['uid'], 'submit' => 'true'),
				'action' => 'index.php?home=login&act=editpassword',
				'title' => "修改密码",
				'body' => $body,
				'icon' => "",
				'button1' => "提交修改",
				'sumbmit_javascript' => $submit_javascript
			);
			include template('confirmbox');
		} else {
			$newpassword = getgpc('newpassword');
			$password = md5($newpassword);
			
			$result = $this->users->UpdateData(array('password' => $password), " AND uid='$_G[uid]'");
			if($result) {
				$_SESSION['message'] = array('code' => '1', 'content' => array('已成功修改密码'));
			} else {
				$_SESSION['message'] = array('code' => '-1', 'content' => array('修改密码失败'));
			}
			header("Location: {$_SERVER['HTTP_REFERER']}");
		}
	}

	public function editpasswords_action() {
		global $_G;
		if(!submitcheck('submit')) {
			$body .= "<label>新密码</label><input type='password' name='newpassword' id='newpassword' />
			<label>再次输入新密码</label><input type='password' id='repeat_newpassword' />";
			$submit_javascript = <<<EOF
<script type="text/javascript">
$(function(){
	$("#modal_form").submit(function(){
		var newpassword = $("#newpassword").val();
		var repeat_newpassword = $("#repeat_newpassword").val();
		if(newpassword == '') {
			alert('输入的密码不能为空');
			location.href = location.href;
			return false;
		}
		if(newpassword != repeat_newpassword) {
			alert('两次输入的密码不一致');
			location.href = location.href;
			return false;
		}
	});
});
</script>
EOF;
			$cfm_box = array(
				'input' => array('uid' => $_G['uids'], 'submit' => 'true'),
				'action' => 'index.php?home=login&act=editpasswords&uids='.$_G['uids'],
				'title' => "修改密码",
				'body' => $body,
				'icon' => "",
				'button1' => "提交修改",
				'sumbmit_javascript' => $submit_javascript
			);
			include template('confirmbox');
		} else {
			$newpassword = getgpc('newpassword');
			$password = md5($newpassword);
			
			$result = $this->users->UpdateData(array('password' => $password), " AND uid='$_G[uids]'");
			if($result) {
				$_SESSION['message'] = array('code' => '1', 'content' => array('已成功修改密码'));
			} else {
				$_SESSION['message'] = array('code' => '-1', 'content' => array('修改密码失败'));
			}
			header("Location: {$_SERVER['HTTP_REFERER']}");
		}
		
	}*/
}
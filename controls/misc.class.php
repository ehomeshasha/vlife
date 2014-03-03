<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class misc_controller {

	public function __construct() {
		include ROOT_PATH.'./models/common.php';
		$this->businesslog = new common('businesslog');
		$this->forum_thread = new common('forum_thread');
		$this->users = new common('users');
	}
	public function index_action() {
				
	}
	public function clear_session_action() {
		global $session;
		$session->destroy('message');
	}
	public function cancel_upload_action() {
		global $_G;
		$bid = getgpc('bid');
		$path = getgpc('path');
		
		$businesslog = $this->businesslog->GetOne(" AND bid='$bid'", array('filepath','uid'));
		//print_r($businesslog);
		 
		if($_G['userlevel'] != 9 && $_G['uid'] != $businesslog['uid']) showmessage("您没有权限进行此操作");
		$filepathArr = explode(",", $businesslog['filepath']);
		foreach ($filepathArr as $v) {
			if($v == $path) continue;
			$filepathArr2[] = $v;
		}
		$filepath2 = implode(",", $filepathArr2);
		$this->businesslog->UpdateData(array('filepath'=>$filepath2), " AND bid='$bid'");
		$pathArr = explode("^", $path);
		unlink($pathArr[1]);
	}
	public function adduserlevel_action() {
		global $_G;
		if($_G['userlevel'] != 9) exit;
		$businesslog = $this->businesslog->getAll();
		$thread = $this->forum_thread->getAll();
		foreach ($businesslog as $v) {
			$user = $this->users->getOne(' AND uid='.$v['uid']);
			$userlevel = $user['userlevel'];
			$this->businesslog->UpdateData(array('userlevel'=>$userlevel), ' AND bid='.$v['bid']);
		}
		
		foreach ($thread as $v) {
			$user = $this->users->getOne(' AND uid='.$v['uid']);
			$userlevel = $user['userlevel'];
			$this->forum_thread->UpdateData(array('userlevel'=>$userlevel), ' AND tid='.$v['tid']);
		}
	}
	public function addviews_action() {
		$tid = getgpc('id');
		$this->forum_thread->UpdateData(array('views'=>'views+1'), " AND tid='$tid'", '', '');
	}
}

<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class misc_controller {

	public function __construct() {
		
	}
	public function index_action() {
				
	}
	public function clear_session_action() {
		global $session;
		$session->destroy('message');
	}
	public function cancel_upload_action() {
		global $_G;
		$table_name = getgpc('table_name');
		$id = getgpc('id');
		$path = getgpc('path');
		
		$record = $GLOBALS['db']->fetch_first("SELECT uid,filepath FROM ".tname($table_name)." WHERE id='$id'");
		if($_G['userlevel'] != $_G['setting']['userlevel']['superadmin'] && $_G['uid'] != $record['uid']) {
			showmessage("You'v no right to cancel this uploaded file");	
		}
		$filepathArr = explode(",", $record['filepath']);
		print_r($filepathArr);
		foreach ($filepathArr as $v) {
			if($v == $path) continue;
			$filepathArr2[] = $v;
		}
		$GLOBALS['db']->query("UPDATE ".tname($table_name)." SET filepath='$filepath2' WHERE id='$id'");
		$pathArr = explode("^", $path);
		unlink(ROOT_PATH.$pathArr[1]);
	}
	
}

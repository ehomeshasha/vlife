<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class admusers_controller {
	public function __construct() {
		include ROOT_PATH.'./models/common.php';
		$this->users = new common('users');
	}
	public function index_action() {
		
		global $_G;
		
		//if($_G['userlevel'] != 9 && $_G['userlevel'] != 2) showresult("您无权进行此操作");
		$perpage = getgpc('perpage');
		if(!empty($perpage)) {
			$_SESSION['perpage'] = $perpage;
		} else {
			$perpage = empty($_SESSION['perpage'])? $_G['setting']['perpage'] : $_SESSION['perpage'];
		}
		
		$page = empty($_GET['page'])?0:intval($_GET['page']);
		if($page<1) $page=1;
		$start = ($page-1)*$perpage;
		
		$date = initdate();
		$username = getgpc('username');
		
		
		$where = empty($username) ? "" : " WHERE username LIKE '%$username%'";
		
		$userlist = $GLOBALS['db']->fetch_all("SELECT uid,username,userlevel,manager,PM,dateline FROM oa_users".$where);

		
		foreach ($userlist as $k=>$v) {
			//echo " un=".$v['uid'];
			if($v['dateline'] != ""){
				$v['dateline']= date("Y-m-d", $v['dateline']);
			}else{
				$v['dateline'] = "";
			}
			
			$datalist[] = $v;
			//echo " uu=".$datalist[2];
		}
		
		
		$url = "index.php?home=admusers";
		$multi = multi($count, $perpage, $page, $url);
		
		include template('admuserslist');
	}

	public function adm_ul_action() {
		
		global $_G;
		if($_G['userlevel'] != 9 && $_G['userlevel'] != 2) exit('你无权进行此操作');
		$uid = getgpc('uid');
		$user_level = getgpc('score');

		
				$GLOBALS['db']->query("UPDATE ".tname('users')." SET userlevel ='$user_level' WHERE uid='$uid'");
					
			
				$_SESSION['message'] = array('code' => '1', 'content' => array('已成功修改用户级别'));
				echo '已成功修改用户级别';
			
		
			//header("Location: {$_SERVER['HTTP_REFERER']}");
		
	}
	public function adm_dt_action() {
		
		global $_G;
		if($_G['userlevel'] != 9 && $_G['userlevel'] != 2) exit('你无权进行此操作');
		$uid = getgpc('uid');
		$manager = getgpc('score');

		
				$GLOBALS['db']->query("UPDATE ".tname('users')." SET manager ='$manager' WHERE uid='$uid'");
					
			
				$_SESSION['message'] = array('code' => '1', 'content' => array('已成功修改用户级别'));
				echo '已成功修改用户级别';
			
		
			//header("Location: {$_SERVER['HTTP_REFERER']}");
		
	}
	public function adm_pm_action() {
		
		global $_G;
		if($_G['userlevel'] != 9 && $_G['userlevel'] != 2) exit('你无权进行此操作');
		$uid = getgpc('uid');
		$pm = getgpc('score');

		
				$GLOBALS['db']->query("UPDATE ".tname('users')." SET PM ='$pm' WHERE uid='$uid'");
					
			
				$_SESSION['message'] = array('code' => '1', 'content' => array('已成功修改该用户的PM用户组'));
				echo '已成功修改用户级别';
			
		
			//header("Location: {$_SERVER['HTTP_REFERER']}");
		
	}


}


?>
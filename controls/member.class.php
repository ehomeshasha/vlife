<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class member_controller {
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
		
		$where = "userlevel != 9 AND userlevel !=-1";
		if($_G['userlevel'] != 9 && $_G['userlevel'] != 2) {
			$where .= " AND uid='$_G[uid]'";
		}
		
		$where .= empty($username) ? "" : " AND username LIKE '%$username%'";
		
		$where2 .= " AND dateline>'{$date['starttime']}' AND dateline<'{$date['endtime']}'";
		
		$userlist = $this->users->getAll(" AND ".$where, array(), "ORDER BY userlevel DESC", "LIMIT $start, $perpage");
		$count = getcount('users', $where);	
		
		foreach ($userlist as $k=>$v) {
			$v['logcount'] = getcount('businesslog', "uid='$v[uid]' AND status>=0 {$where2}");
			$thread = $GLOBALS['db']->fetch_first("SELECT COUNT(*) AS count, SUM(score_value) AS total, AVG(score_value) AS average FROM ".tname('forum_thread')." WHERE uid='$v[uid]' AND status>=0 {$where2}");
			$v['threadcount'] = $thread['count'];
			$v['threadaveragescore'] = $thread['average'];
			$v['threadtotalscore'] = $thread['total'];
			$datalist[] = $v;
		}
		
		
		$url = "index.php?home=member";
		$multi = multi($count, $perpage, $page, $url);
		
		include template('memberlist');
	}
}


?>
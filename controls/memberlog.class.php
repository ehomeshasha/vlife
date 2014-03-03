<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class memberlog_controller {
	//private $dd_ll;
	//public $dd_ll;

	public function __construct() {
		include ROOT_PATH.'./models/common.php';
		$this->users = new common('users');
	}
	public function index_action() {
		
		global $_G;

		//$dd_ll = "321";
		
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
		$d_l = $date['endtime'];
		setcookie('score_dl',$d_l,time()+3600);
		$year = date("Y",$date['endtime']);
		$month = date("m",$date['endtime']);
		$allday = date("t",$date['endtime']);
		
		
		$strat_time = strtotime($year."-".$month."-1");
		$end_time = strtotime($year."-".$month."-".$allday)+85400;
		
		//echo " dt=".$end_time;
		$where2 .= " AND dateline>='{$strat_time}' AND dateline<='{$end_time}'";
		$where3 .= " AND s.dateline>='{$strat_time}' AND s.dateline<='{$end_time}'";
		
		$userlist = $this->users->getAll(" AND ".$where, array(), "ORDER BY userlevel DESC", "LIMIT $start, $perpage");
		$count = getcount('users', $where);	
		
		foreach ($userlist as $k=>$v) {
			
			$v['logcount'] = getcount('businesslog', "uid='$v[uid]' AND status>=0 {$where2}");
			
			$thread = $GLOBALS['db']->fetch_first("SELECT u.uid , u.username , u.userlevel , u.manager , s.log_score , s.team_score , s.innovate , s.dateline , s.txt FROM ".tname('users')." as u Left outer join ".tname('member_score')." as s ON u.uid = s.uid WHERE u.uid='$v[uid]' {$where3}");
			$v['log_score'] = $thread['log_score'];
			$v['team_score'] = $thread['team_score'];
			$v['innovate'] = $thread['innovate'];
			
			$v['score_count'] = $v['log_score'] + $v['team_score'] + $v['innovate'];
			$v['txt']=$thread['txt'];
			
			if($thread['dateline'] != ""){
				$v['dateline']= date("Y-m-d", $thread['dateline']);
			}else{
				$v['dateline'] = "";
			}
			$datalist[] = $v;
		}
		
		
		$url = "index.php?home=memberlog";
		$multi = multi($count, $perpage, $page, $url);
		
		include template('memberloglist');
	}

	public function score_log_action() {
		
		global $_G;
		if($_G['userlevel'] != 9 && $_G['userlevel'] != 2) exit('你无权进行此操作');
		$uid = getgpc('uid');
		$dd_l = $_COOKIE["score_dl"];
		$year = date("Y",$dd_l);
		$month = date("m",$dd_l);
		$allday = date("t",$dd_l);
		$allday_s = date("t",$dd_l)-1;
		$strat_time = strtotime($year."-".$month."-1");
		$end_time = strtotime($year."-".$month."-".$allday);
		$score_time = strtotime($year."-".$month."-".$allday_s);

		$where3 .= "AND s.dateline>'$strat_time' AND s.dateline<'$end_time'";

		$thread = $GLOBALS['db']->fetch_first("SELECT u.uid , u.username , u.userlevel , u.manager , s.log_score , s.team_score , s.innovate , s.dateline , s.txt , s.id FROM ".tname('users')." as u Left outer join ".tname('member_score')." as s ON u.uid = s.uid WHERE u.uid='$uid' {$where3}");
		
		$ms_id = $thread['id'];
		$dateline_score = time();

			$score_level = getgpc('score');
			if($score_level == -1 || $score_level == "") {
				$GLOBALS['db']->query("UPDATE ".tname('member_score')." SET log_score=0 WHERE id='$ms_id'");
				//$_SESSION['message'] = array('code' => '1', 'content' => array('已成功取消评分'));
				echo '已成功取消评分';
			} else {
				if($thread['id']){
					$GLOBALS['db']->query("UPDATE ".tname('member_score')." SET log_score ='$score_level' WHERE id='$ms_id'");
					
				}else{
					$GLOBALS['db']->query("insert into ".tname('member_score')." (uid,log_score,dateline) values ('$uid','$score_level','$score_time')");
					
				}
			
				$_SESSION['message'] = array('code' => '1', 'content' => array('已成功打分'));
				echo '已成功打分';
			}
			
			//header("Location: {$_SERVER['HTTP_REFERER']}");
		
	}

	public function score_team_action() {
		global $_G;
		if($_G['userlevel'] != 9 && $_G['userlevel'] != 2) exit('你无权进行此操作');
		$uid = getgpc('uid');
		
		$dd_l = $_COOKIE["score_dl"];
		$year = date("Y",$dd_l);
		$month = date("m",$dd_l);
		$allday = date("t",$dd_l);
		$allday_s = date("t",$dd_l)-1;
		$strat_time = strtotime($year."-".$month."-1");
		$end_time = strtotime($year."-".$month."-".$allday);
		$score_time = strtotime($year."-".$month."-".$allday_s);

		$where3 .= "AND s.dateline>'$strat_time' AND s.dateline<'$end_time'";

		$thread = $GLOBALS['db']->fetch_first("SELECT u.uid , u.username , u.userlevel , u.manager , s.log_score , s.team_score , s.innovate , s.dateline , s.txt , s.id FROM ".tname('users')." as u Left outer join ".tname('member_score')." as s ON u.uid = s.uid WHERE u.uid='$uid' {$where3}");

		$ms_id = $thread['id'];
		$dateline_score = time();

			$score_level = getgpc('score');
			if($score_level == -1 || $score_level == "") {
				$GLOBALS['db']->query("UPDATE ".tname('member_score')." SET team_score=0 WHERE id='$ms_id'");
				//$_SESSION['message'] = array('code' => '1', 'content' => array('已成功取消评分'));
				echo '已成功取消评分';
			} else {
				if($thread['id']){
					$GLOBALS['db']->query("UPDATE ".tname('member_score')." SET team_score ='$score_level' WHERE id='$ms_id'");
					
				}else{
					$GLOBALS['db']->query("insert into ".tname('member_score')." (uid,team_score,dateline) values ('$uid','$score_level','$score_time')");
					
				}
			
				$_SESSION['message'] = array('code' => '1', 'content' => array('已成功打分'));
				echo '已成功打分';
			}
			
			//header("Location: {$_SERVER['HTTP_REFERER']}");
		
	}

	public function score_innovate_action() {
		global $_G;
		if($_G['userlevel'] != 9 && $_G['userlevel'] != 2) exit('你无权进行此操作');
		$uid = getgpc('uid');
		
		$dd_l = $_COOKIE["score_dl"];
		$year = date("Y",$dd_l);
		$month = date("m",$dd_l);
		$allday = date("t",$dd_l);
		$allday_s = date("t",$dd_l)-1;
		$strat_time = strtotime($year."-".$month."-1");
		$end_time = strtotime($year."-".$month."-".$allday);
		$score_time = strtotime($year."-".$month."-".$allday_s);

		$where3 .= "AND s.dateline>'$strat_time' AND s.dateline<'$end_time'";

		$thread = $GLOBALS['db']->fetch_first("SELECT u.uid , u.username , u.userlevel , u.manager , s.log_score , s.team_score , s.innovate , s.dateline , s.txt , s.id FROM ".tname('users')." as u Left outer join ".tname('member_score')." as s ON u.uid = s.uid WHERE u.uid='$uid' {$where3}");

		$ms_id = $thread['id'];
		$dateline_score = time();

			$score_level = getgpc('score');
			if($score_level == -1 || $score_level == "") {
				$GLOBALS['db']->query("UPDATE ".tname('member_score')." SET innovate=0 WHERE id='$ms_id'");
				//$_SESSION['message'] = array('code' => '1', 'content' => array('已成功取消评分'));
				echo '已成功取消评分';
			} else {
				if($thread['id']){
					$GLOBALS['db']->query("UPDATE ".tname('member_score')." SET innovate ='$score_level' WHERE id='$ms_id'");
					
				}else{
					$GLOBALS['db']->query("insert into ".tname('member_score')." (uid,innovate,dateline) values ('$uid','$score_level','$score_time')");
					
				}
			
				$_SESSION['message'] = array('code' => '1', 'content' => array('已成功打分'));
				echo '已成功打分';
			}
			
			//header("Location: {$_SERVER['HTTP_REFERER']}");
		
	}

	public function score_txt_action() {
		global $_G;
		if($_G['userlevel'] != 9 && $_G['userlevel'] != 2) exit('你无权进行此操作');
		$uid = getgpc('uid');
		$dd_l = $_COOKIE["score_dl"];
		$year = date("Y",$dd_l);
		$month = date("m",$dd_l);
		$allday = date("t",$dd_l);
		$strat_time = strtotime($year."-".$month."-1");
		$end_time = strtotime($year."-".$month."-".$allday);


		$where3 .= "AND s.dateline>'$strat_time' AND s.dateline<'$end_time'";

		$thread = $GLOBALS['db']->fetch_first("SELECT u.uid , u.username , u.userlevel , u.manager , s.log_score , s.team_score , s.innovate , s.dateline , s.txt , s.id FROM ".tname('users')." as u Left outer join ".tname('member_score')." as s ON u.uid = s.uid WHERE u.uid='$uid' {$where3}");

		$ms_id = $thread['id'];
		$dateline_score = time();

			$score_txt = getgpc('score');
			
			if($score_txt == "") {
				$GLOBALS['db']->query("UPDATE ".tname('member_score')." SET txt='' WHERE id='$ms_id'");
				
			} else {
				if($thread['id']){
					$GLOBALS['db']->query("UPDATE ".tname('member_score')." SET txt ='$score_txt' WHERE id='$ms_id'");
					
				}else{
					echo "您还没有打分";
				}
				
				$_SESSION['message'] = array('code' => '1', 'content' => array('已成功忝加考评详细'));
				echo '已成功忝加考评详细';
			}
			
			//header("Location: {$_SERVER['HTTP_REFERER']}");
		
	}
	

}


?>
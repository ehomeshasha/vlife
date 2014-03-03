<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class index_controller {

	//构造函数
	public function __construct() {
		include ROOT_PATH.'./models/common.php';
		$this->businesslog = new common('businesslog');
		$this->forum_thread = new common('forum_thread');
		$this->bulletin = new common('bulletin');
		$this->users = new common('users');
		$this->reply = new common('reply');
	}
	
	public function index_action() {
		global $_G;
		
		$where = " AND status>=0";
		//公告统计
		$bulletinlist = $this->bulletin->getAll($where, array('title', 'content', 'dateline'), "ORDER BY dateline DESC", "LIMIT 0,5");
		$bulletinlist = fill_tablerow_with_blank($bulletinlist, 8);
		//count统计
		$statistic_date = get_statistic_date();
		$statistic_count = get_statistic_count($statistic_date);
		$where2 .= $where." AND uid='$_G[uid]'"; 
		$order = "ORDER BY dateline DESC";
		$limit = "LIMIT 0,5";
		//日志概况
		$businesslog_viewmelist = $this->businesslog->getAll($where2, array('bid', 'todayplan', 'dateline'), $order, $limit);
		$businesslog_viewmelist = fill_tablerow_with_blank($businesslog_viewmelist);
		
		$businesslogall = $this->businesslog->getAll($where, array('bid', 'todayplan', 'username','dateline', 'sendlist'), $order);
		$businesslog_mentionme_count = 0;
		foreach ($businesslogall as $val) {
			if($businesslog_mentionme_count == 5) break;
			$arr2 = explode(',', $val['sendlist']);
			if(!in_array($_G['username'], $arr2)) continue;
			$businesslog_mentionmelist[] = $val;
			$businesslog_mentionme_count++;
		}
		$businesslog_mentionmelist = fill_tablerow_with_blank($businesslog_mentionmelist);
		
		$businesslogreplylist = $GLOBALS['db']->fetch_all("SELECT a.id,a.fid,a.reply,a.dateline FROM ".tname('reply')." AS a 
		LEFT JOIN ".tname('businesslog')." AS b ON a.fid=b.bid 
		WHERE a.status>=0 AND a.uid='$_G[uid]' AND a.replytype=0 AND b.status>=0
		ORDER BY dateline DESC LIMIT 0, 5");
		$businesslogreplylist = fill_tablerow_with_blank($businesslogreplylist);
		
		$businesslogreplytomelist = $GLOBALS['db']->fetch_all("SELECT a.id,a.fid,a.reply,a.username,a.dateline FROM ".tname('reply')." AS a 
		LEFT JOIN ".tname('businesslog')." AS b ON a.fid=b.bid 
		WHERE a.status>=0 AND a.replytype=0 AND b.status>=0 AND b.uid='$_G[uid]' AND a.uid!='$_G[uid]'
		ORDER BY dateline DESC LIMIT 0, 5");
		$businesslogreplytomelist = fill_tablerow_with_blank($businesslogreplytomelist);
		//帖子概况
		$thread_viewmelist = $this->forum_thread->getAll($where2, array('tid', 'title', 'dateline'), $order, $limit);
		$thread_viewmelist = fill_tablerow_with_blank($thread_viewmelist);
		$thread_mentionme_count = 0;
		$threadall = $this->forum_thread->getAll($where, array('tid', 'title', 'username','dateline', 'sendlist'), $order);
		
		foreach ($threadall as $val) {
			if($thread_mentionme_count == 5) break;
			$arr2 = explode(',', $val['sendlist']);
			if(!in_array($_G['username'], $arr2)) continue;
			$thread_mentionmelist[] = $val;
			$thread_mentionme_count++;
		}
		$thread_mentionmelist = fill_tablerow_with_blank($thread_mentionmelist);
		
		$threadreplylist = $GLOBALS['db']->fetch_all("SELECT a.id,a.fid,a.reply,a.dateline FROM ".tname('reply')." AS a 
		LEFT JOIN ".tname('forum_thread')." AS b ON a.fid=b.tid 
		WHERE a.status>=0 AND a.uid='$_G[uid]' AND a.replytype=1 AND b.status>=0
		ORDER BY dateline DESC LIMIT 0, 5");
		$threadreplylist = fill_tablerow_with_blank($threadreplylist);
		
		$threadreplytomelist = $GLOBALS['db']->fetch_all("SELECT a.id,a.fid,a.reply,a.username,a.dateline FROM ".tname('reply')." AS a 
		LEFT JOIN ".tname('forum_thread')." AS b ON a.fid=b.tid 
		WHERE a.status>=0 AND a.replytype=1 AND b.status>=0 AND b.uid='$_G[uid]' AND a.uid!='$_G[uid]'
		ORDER BY dateline DESC LIMIT 0, 5");
		$threadreplytomelist = fill_tablerow_with_blank($threadreplytomelist);
		
		$statistics_where = " AND dateline>'{$statistic_date['starttime']}' AND dateline<'{$statistic_date['endtime']}'";
		$statistics = $GLOBALS['db']->fetch_first("SELECT SUM(score_value) AS total, AVG(score_value) AS average FROM ".tname('forum_thread')." WHERE uid='$_G[uid]' AND status>=0 {$statistics_where}");
		$statistics['average'];
		$statistics['total'];
		
		
		
		
		
		
		
		
		
		
		
		
		
		include template('index');
	}
	
	/*
	 * 初始化user的count
	 * $users = $this->users->getAll("");
		foreach ($users as $v) {
			$businesslogcount = getcount('businesslog',"uid=$v[uid]");
			$threadcount = getcount('forum_thread',"uid=$v[uid]");
			$businesslogreplycount = getcount('reply',"uid=$v[uid] AND replytype=0");
			$threadreplycount = getcount('reply',"uid=$v[uid] AND replytype=1");
			$data = array(
				'businesslogcount' => $businesslogcount,
				'threadcount' => $threadcount, 
				'businesslogreplycount' => $businesslogreplycount, 
				'threadreplycount' => $threadreplycount
			);
			$this->users->UpdateData($data, " AND uid=$v[uid]");
		}
	 */
}

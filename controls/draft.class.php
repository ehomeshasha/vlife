<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class draft_controller {

	public function __construct() {
		include ROOT_PATH.'./models/common.php';
		$this->businesslog = new common('businesslog');
		$this->forum_thread = new common('forum_thread');
		$this->users = new common('users');
	}
	
	public function index_action() {
		global $_G;
		$type = getgpc('type');
		$typearr = array('log', 'thread');
		if(empty($type) || !in_array($type, $typearr)) $type = 'log';
			
		$where = "status=-2 AND uid='$_G[uid]'";
		$url = "index.php?home=draft";
			
		if($type == 'log') {
			$log_active = "class='active'";
			$typename = 'businesslog';
			$tablename = 'businesslog';
			$column1 = 'bid';
			$column2 = 'todayplan';
		} elseif($type == 'thread') {
			$thread_active = "class='active'";
			$typename = 'thread';
			$tablename = 'forum_thread';
			$column1 = 'tid';
			$column2 = 'title';
			
		}
		
		
		$editlink = "index.php?home={$typename}&act=edit&bid=";
		$perpage = getgpc('perpage');
		if(!empty($perpage)) {
			$_SESSION['perpage'] = $perpage;
		} else {
			$perpage = empty($_SESSION['perpage'])? $_G['setting']['perpage'] : $_SESSION['perpage'];
		}
		
		$page = empty($_GET['page'])?0:intval($_GET['page']);
		if($page<1) $page=1;
		$start = ($page-1)*$perpage;
		
		$count = getcount($tablename, $where);	
		$url .= "&type={$type}";
		$multi = multi($count, $perpage, $page, $url);
		
		$datelist = $GLOBALS['db']->fetch_all("SELECT `{$column1}` AS id, `{$column2}` AS content, username, dateline FROM ".tname($tablename)." WHERE $where ORDER BY dateline DESC LIMIT $start, $perpage");
		$businesslogcount = getcount('businesslog', $where);	
		$threadcount = getcount('forum_thread', $where);
		$draftcount = $businesslogcount + $threadcount;
		
		include template('draft');
		
	}
	
	public function post_action() {
		
		global $_G;
		
		$id = getgpc('id');
		$type = getgpc('type');
		$typearr = array('log', 'thread');
		if(empty($type) || !in_array($type, $typearr)) $type = 'log';
		
		if($type == 'log') {
			$typename = "工作日志";
			$table = $this->businesslog;
			$column = 'bid';
		} elseif($type == 'thread') {
			$typename = "帖子";
			$table = $this->forum_thread;
			$column = 'tid';
		}
		
		$res = $table->GetOne(" AND `{$column}`='$id'", array('uid'));
		if($res['uid'] != $_G['uid']) showresult("只有作者自己可以发布草稿箱里的{$typename}");
		
		if(!submitcheck('submit')) {
			
			$cfm_box = array(
			'input' => array('id' => $id, 'submit' => 'true'),
			'action' => "index.php?home=draft&act=post&type=$type",
			'title' => "发布操作",
			'body' => "确定要发布这条{$typename}吗？",
			'icon' => "icon-question-sign",
			'button1' => "确定",
			'button2' => "不了，谢谢"
			);
			include template('confirmbox');
			
		} else {
			
			$result = $table->UpdateData(array('status'=>'0')," AND `{$column}`='$id'");
			
			if($result) {
				$_SESSION['message'] = array('code' => '1', 'content' => array("已成功发布这条{$typename}(现在所有人都可以在{$typename}列表中看到你的{$typename}了)"));
			} else {
				$_SESSION['message'] = array('code' => '-1', 'content' => array("发布{$typename}失败"));
			}
			
			header('Location: index.php?home=draft&type='.$type);
		}
	}
	
	public function bulk_post_action() {
		global $_G;
		$id = getgpc('id');
		if(empty($id)) {
			$_SESSION['message'] = array('code' => '-1', 'content' => array("没有选择发布对象"));
			header('Location: index.php?home=draft&type='.$type);
			exit;
		}
		
		$idstr = dimplode($id);
		$type = getgpc('type');
		$typearr = array('log', 'thread');
		if(empty($type) || !in_array($type, $typearr)) $type = 'log';
		if($type == 'log') {
			$table = $this->businesslog;
			$column = 'bid';
		} elseif($type == 'thread') {
			$table = $this->forum_thread;
			$column = 'tid';
		}
		$result = $table->UpdateData(array('status'=>'0')," AND `{$column}` IN($idstr)");
		if($result) {
			$_SESSION['message'] = array('code' => '1', 'content' => array("发布成功"));
		} else {
			$_SESSION['message'] = array('code' => '-1', 'content' => array("发布失败"));
		}
		header('Location: index.php?home=draft&type='.$type);
		
	}
	
	public function remove_action() {
		global $_G;
		
		$id = getgpc('id');
		$type = getgpc('type');
		$typearr = array('log', 'thread');
		if(empty($type) || !in_array($type, $typearr)) $type = 'log';
		
		if($type == 'log') {
			$typename = "工作日志";
			$table = $this->businesslog;
			$column = 'bid';	
		} elseif($type == 'thread') {
			$typename = "论坛帖子";
			$table = $this->forum_thread;
			$column = 'tid';
		}
		
		$res = $table->GetOne(" AND `{$column}`='$id'", array('uid'));
		if($res['uid'] != $_G['uid']) showresult("只有作者自己可以删除草稿箱里的{$typename}");
		
		
		
		if(!submitcheck('submit')) {
			
			$cfm_box = array(
			'input' => array('id' => $id, 'submit' => 'true'),
			'action' => "index.php?home=draft&act=remove&type=$type",
			'title' => "删除操作",
			'body' => "确定要删除这条{$typename}吗？(彻底删除无法恢复)",
			'icon' => "icon-question-sign",
			'button1' => "确定",
			'button2' => "不了，谢谢"
			);
			include template('confirmbox');
			
		} else {
			
			$result = $table->DeleteData("`{$column}`='$id'");
			if($result) {
				$_SESSION['message'] = array('code' => '1', 'content' => array("已成功从草稿箱中删除{$typename}"));
			} else {
				$_SESSION['message'] = array('code' => '-1', 'content' => array("删除{$typename}失败"));
			}
			
			header('Location: index.php?home=draft&type='.$type);
		}
	}
	
	
}
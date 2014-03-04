<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class recyclebin_controller {

	public function __construct() {
		include ROOT_PATH.'./models/common.php';
		$this->businesslog = new common('businesslog');
		$this->forum_thread = new common('forum_thread');
		$this->bulletin = new common('bulletin');
		$this->users = new common('users');
	}
	
	public function index_action() {
		global $_G;
		if($_G['userlevel'] != 9) showresult("只有管理员才可进行此操作");
		$type = getgpc('type');
		$typearr = array('log', 'thread', 'bulletin');
		if(empty($type) || !in_array($type, $typearr)) $type = 'log';
		
		$where = "status=-1";
		$url = "index.php?home=recyclebin";
		
		$perpage = getgpc('perpage');
		if(!empty($perpage)) {
			$_SESSION['perpage'] = $perpage;
		} else {
			$perpage = empty($_SESSION['perpage'])? $_G['setting']['perpage'] : $_SESSION['perpage'];
		}
		
		$page = empty($_GET['page'])?0:intval($_GET['page']);
		if($page<1) $page=1;
		$start = ($page-1)*$perpage;
			
		if($type == 'log') {
			$log_active = "class='active'";
			$viewlink = "index.php?home=businesslog&act=view&bid=";
			$extword1 = "bid AS";
			$extword2 = "todayplan AS";
			$tablename = 'businesslog';
		} elseif($type == 'thread') {
			$thread_active = "class='active'";
			$extword1 = "tid AS";
			$extword2 = "title AS";
			$extword3 = ", link";
			$tablename = 'forum_thread';
		} elseif($type == 'bulletin') {
			$bulletin_active = "class='active'";
			$tablename = 'bulletin';
		}
		
		$url .= "&type=".$type;
		$count = getcount($tablename, $where);
		$datalist = $GLOBALS['db']->fetch_all("SELECT {$extword1} id, {$extword2} content, username, dateline {$extword3} FROM ".tname($tablename)." WHERE $where ORDER BY dateline DESC LIMIT $start, $perpage");
		$multi = multi($count, $perpage, $page, $url);
		
		include template('recyclebin');
	}
	
	public function recover_action() {
		
		global $_G;
		if($_G['userlevel'] != 9) showresult("只有管理员才可进行此操作");
		$id = getgpc('id');
		$type = getgpc('type');
		$authorid = getgpc('authorid');
		$typearr = array('log', 'thread', 'bulletin');
		if(empty($type) || !in_array($type, $typearr)) $type = 'log';
		
		if($type == 'log') {
			$typename = "工作日志";
			$table = $this->businesslog;
			$column = 'bid';
			$column2 = 'businesslogcount';
		} elseif($type == 'thread') {
			$typename = "论坛帖子";
			$table = $this->forum_thread;
			$column = 'tid';
			$column2 = 'threadcount';
		} elseif($type == 'bulletin') {
			$typename = "OA公告";
			$table = $this->bulletin;
			$column = 'id';
		} 
		
		if(!submitcheck('submit')) {
			
			$cfm_box = array(
			'input' => array('id' => $id, 'submit' => 'true'),
			'action' => "index.php?home=recyclebin&act=recover&type=$type",
			'title' => "恢复操作",
			'body' => "确定要恢复这条{$typename}吗？",
			'icon' => "icon-question-sign",
			'button1' => "确定",
			'button2' => "不了，谢谢"
			);
			include template('confirmbox');
			
		} else {
			
			$result = $table->UpdateData(array('status'=>'0')," AND `{$column}`='$id'");
			
			if($result) {
				$_SESSION['message'] = array('code' => '1', 'content' => array("已成功恢复这条{$typename}"));
			} else {
				$_SESSION['message'] = array('code' => '-1', 'content' => array("恢复{$typename}失败"));
			}
			
			header('Location: index.php?home=recyclebin&type='.$type);
		}
	}
	
	public function remove_action() {
		global $_G;
		if($_G['userlevel'] != 9) showresult("只有管理员才可进行此操作");
		$id = getgpc('id');
		$type = getgpc('type');
		$typearr = array('log', 'thread', 'bulletin');
		if(empty($type) || !in_array($type, $typearr)) $type = 'log';
		
		if($type == 'log') {
			$typename = "工作日志";
			$table = $this->businesslog;
			$column = 'bid';	
		} elseif($type == 'thread') {
			$typename = "论坛帖子";
			$table = $this->forum_thread;
			$column = 'tid';
		} elseif($type == 'bulletin') {
			$typename = "OA公告";
			$table = $this->bulletin;
			$column = 'id';
		}
		
		if(!submitcheck('submit')) {
			
			$cfm_box = array(
			'input' => array('id' => $id, 'submit' => 'true'),
			'action' => "index.php?home=recyclebin&act=remove&type=$type",
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
				$_SESSION['message'] = array('code' => '1', 'content' => array("已成功从回收站中删除{$typename}"));
			} else {
				$_SESSION['message'] = array('code' => '-1', 'content' => array("删除{$typename}失败"));
			}
			
			header('Location: index.php?home=recyclebin&type='.$type);
		}
	}
}
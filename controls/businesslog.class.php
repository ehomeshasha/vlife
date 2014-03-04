<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class businesslog_controller {

	public function __construct() {
		include ROOT_PATH.'./models/common.php';
		$this->businesslog = new common('businesslog');
		$this->reply = new common('reply');
		$this->users = new common('users');
	}
	
	public function index_action() {
		global $_G;
		
		if(file_exists(ROOT_PATH.'/data/cache/userlist.php')) {
			$userlist = read('userlist');
		} else {
			$userlist = $this->users->getAll(' AND uid !=60 AND userlevel !=-1', array('uid','username','userlevel','manager','PM','dateline'), "ORDER BY userlevel DESC");
			write('userlist', $userlist);
		}
		
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
		$authorid = getgpc('authorid');
		$todayplan = getgpc('todayplan');
		$view = getgpc('view');
		$mention = getgpc('mention');
		$userlevel = getgpc('userlevel');
		
		$where = "status>=0";
		$where .= " AND dateline>'{$date['starttime']}' AND dateline<'{$date['endtime']}'";
		$where .= empty($authorid) ? "" : " AND uid='$authorid'";
		$where .= empty($todayplan) ? "" : " AND todayplan LIKE '%$todayplan%'";
		$where .= empty($view) ? "" : " AND uid='$_G[uid]'";
		$where .= $userlevel == "" ? "" : " AND userlevel='$userlevel'";
		
		$businessloglist_ori = $this->businesslog->getAll(" AND ".$where, array(), "ORDER BY dateline DESC", "LIMIT $start, $perpage");
		
		if($mention == 'me' || ($_G['userlevel'] != 9 && $_G['userlevel'] != 2)) {
			$businessloglistall_ori = $this->businesslog->getAll(" AND ".$where, array(), "ORDER BY dateline DESC");
			$count = 0;
			foreach ($businessloglistall_ori as $val) {
				$arr2 = explode(',', $val['sendlist']);
				if($mention == 'me') {
					if(!in_array($_G['username'], $arr2)) continue;
				} else {
					if(!in_array($_G['username'], $arr2) && $_G['uid'] != $val['uid']) continue;
				}
				$businessloglistall[] = $val;
				$count++;
			}
			foreach ($businessloglistall as $k=>$v) {
				if($k < $start || $k >= ($start+$perpage)) continue;
				$businessloglist[] = $v;
			}
			
		} else {
			$businessloglist = $businessloglist_ori;
			$count = getcount('businesslog', $where);	
		}
		
		
		
		
		
		
		$url = "index.php?home=businesslog";
		$multi = multi($count, $perpage, $page, $url);
		
		//echo $where;
		include template('businessloglist');
	}
	
	public function post_action() {
		global $_G;
		
		if(!submitcheck('submit')) {
			$cur_location = '发布新日志';
			$uid = $_G['uid'];
			$user_s = $this->users->GetOne(" AND uid=$uid");
			$userlist_html = get_userlist_htmls($_G['action'],$user_s['PM']);
			//echo " pm=".$user_s['PM'];
			include template('businesslog');
		} else {
			
			$todayplan = trim(getgpc('todayplan'));
			$fulfil = trim(getgpc('fulfil'));
			$summary = trim(getgpc('summary'));
			$tomorrowplan = trim(getgpc('tomorrowplan'));
			$filepath = implode(",", getgpc('filepath'));
			
			$checkdata = array(
				'todayplan' => $todayplan,
				'fulfil' => $fulfil,
				'summary' => $summary,
				'tomorrowplan' => $tomorrowplan
			);
			business_check($checkdata);
			
			$sendlistArr = getgpc('sendlist');
			$sendlist = implode(",", $sendlistArr);
			
			$data = array(
				'uid' => $_G['uid'],
				'username' => $_G['username'],
				'userlevel' => $_G['userlevel'],
				'todayplan' => $todayplan,
				'fulfil' => $fulfil,
				'summary' => $summary,
				'tomorrowplan' => $tomorrowplan,
				'sendlist' => $sendlist,
				'filepath' => $filepath,
				'status' => '-2',
				'dateline' => $_G['timestamp']
			);
			$bid = $this->businesslog->insert($data);
			
			showmessage('您已成功发布日志在草稿箱)', 1, 'index.php?home=draft');
		}
	}
	
	public function edit_action() {
		global $_G;
		
		$cur_location = '修改日志';
		$bid = getgpc('bid');
		$businesslog = $this->businesslog->GetOne(" AND bid='$bid'");
		
		if($_G['uid'] != $businesslog['uid'] && $_G['userlevel'] != 9) showresult("你无权进行此操作");
		
		if(!submitcheck('submit')) {
			$uid = $_G['uid'];
			$user_s = $this->users->GetOne(" AND uid=$uid");
			$userlist_html = get_userlist_htmls($_G['action'], $user_s['PM'], $businesslog['sendlist']);
			$filepatharr = explode(",", $businesslog['filepath']);
			
			include template('businesslog');	
		} else {
			
			$todayplan = trim(getgpc('todayplan'));
			$fulfil = trim(getgpc('fulfil'));
			$summary = trim(getgpc('summary'));
			$tomorrowplan = trim(getgpc('tomorrowplan'));
			$filepath = implode(",", getgpc('filepath'));
			
			$checkdata = array(
				'todayplan' => $todayplan,
				'fulfil' => $fulfil,
				'summary' => $summary,
				'tomorrowplan' => $tomorrowplan,
			);
			business_check($checkdata);
			
			$sendlistArr = getgpc('sendlist');
			$sendlist = implode(",", $sendlistArr);
			$data = array(
				'todayplan' => $todayplan,
				'fulfil' => $fulfil,
				'summary' => $summary,
				'tomorrowplan' => $tomorrowplan,
				'sendlist' => $sendlist,
				'filepath' => $filepath
			);
			
			
			$this->businesslog->UpdateData($data, " AND bid='$bid'");
			if($_G['userlevel'] != 9 && $_G['uid'] != $businesslog['uid']) {
				showmessage('您已成功修改日志', 1, 'index.php?home=draft');
			} else {
				showmessage('您已成功修改日志', 1, 'index.php?home=businesslog');
			}
			
			
			
		}
	}
	
	public function view_action() {
		global $_G;
		
		$cur_location = '查看日志';
		$bid = getgpc('bid');
		$businesslog = $this->businesslog->GetOne(" AND bid='$bid'");
		$sendlistArr = explode(",", $businesslog['sendlist']);
		if(!in_array($_G['username'], $sendlistArr) && $_G['uid'] != $businesslog['uid'] && $_G['userlevel'] != 9 && $_G['userlevel'] == 2) showresult("日志只有被@的人或自己才可以查看");	
		if($businesslog['status'] == "-1" && $_G['userlevel'] != '9') showresult("日志在回收站中,只有管理员可以查看");
		if($businesslog['status'] == "-2" && $_G['uid'] != $businesslog['uid'] && $_G['userlevel'] != '9') showresult("日志在草稿箱中,只有作者自己可以查看");
		
		$replies = $this->reply->getAll(" AND fid='$bid' AND status>=0 AND replytype=0", array(), "ORDER BY dateline DESC");
		$userlist_html = "<p>{$businesslog['sendlist']}</p>";
		$this->businesslog->UpdateData(array('views'=>'views+1'), " AND bid='$bid'", '', '');
		
		include template('businesslog');
	}
	
	public function delete_action() {
		global $_G;
		
		$bid = getgpc('bid');
		$authorid = getgpc('authorid');
		
		if($_G['userlevel'] == 9) {
			$str1 = "(日志将被移送到回收站)";
			$str2 = "(可在回收站恢复)";
		}
		if(!submitcheck('submit')) {
			if($_G['uid'] != $authorid && $_G['userlevel'] != 9) showerror("你无权进行此操作");
			
			$cfm_box = array(
				'input' => array('bid' => $bid, 'submit' => 'true', 'authorid' => $authorid),
				'action' => 'index.php?home=businesslog&act=delete',
				'title' => "删除操作",
				'body' => "确定要删除这条工作日志吗？{$str1}",
				'icon' => "icon-question-sign",
				'button1' => "确定",
				'button2' => "不了，谢谢"
			);
			include template('confirmbox');
		} else {
			
			if($_G['uid'] != $authorid && $_G['userlevel'] != 9) showresult("你无权进行此操作");
			if($_G['userlevel'] == 9) {
				$result = $this->businesslog->UpdateData(array('status'=>'-1'), " AND bid='$bid'");
			} else {
				$result = $this->businesslog->DeleteData("bid='$bid'");
			}
			if($result) {
				$_SESSION['message'] = array('code' => '1', 'content' => array("已成功删除日志{$str2}"));
			} else {
				$_SESSION['message'] = array('code' => '-1', 'content' => array('删除日志失败'));
			}
			header('Location: index.php?home=businesslog');
		}
	}
	
	public function reply_action() {
		global $_G;
		$bid = getgpc('bid');
		$ajax = getgpc('ajax');
		if($ajax == 1) {
			$javascript_ajax = "
			var data = $(this).serialize(); 
			$.ajax({
				url:\"index.php?home=businesslog&act=reply&ajax=1\",
				data:data,
				type:'post',
				dataType:'json',
				error:function(){alert('操作失败，请重新尝试');return false;},
				success:function(data){
					//alert(data);
					$('#response_modal').modal('hide');
					var html = data.reply + \"<br /><span class='mrm' style='font-size:12px;'>\" + data.reply_date + \"</span><span>\" + data.username + \"</span>\";
					$('#replybox_{$bid}').html(html);
					var num = $('#replynum_{$bid}').html();
					$('#replynum_{$bid}').html(parseInt(num)+1);
				}
			});
			return false;";
		}
		
		if(!submitcheck('submit')) {
			$body = "<textarea class='big_textarea span5 input_check' id='reply' name='reply' rows='5'></textarea><span class='help-block'>已输入<strong class='inputnum'>0</strong>字</span>";
			$submit_javascript = <<<EOF
<script type="text/javascript">
$(function(){
	$("#modal_form").submit(function(){
		if($("#reply").val() == "") {
			alert('输入的数据不能为空');
			return false;
		}
		$javascript_ajax
	});
});
</script>
EOF;
			$cfm_box = array(
				'input' => array('bid' => $bid, 'submit' => 'true'),
				'action' => 'index.php?home=businesslog&act=reply',
				'title' => "发表回复",
				'body' => $body,
				'icon' => "",
				'button1' => "提交",
				'sumbmit_javascript' => $submit_javascript
			);
			include template('confirmbox');
		} else {
			$reply = getgpc('reply');
			$fid = getgpc('fid') ? getgpc('fid') : $bid;
			
			$data = array(
				'fid' => $fid,
				'uid' => $_G['uid'],
				'username' => $_G['username'],
				'userlevel' => $_G['userlevel'],
				'reply' => $reply,
				'replytype' => '0',
				'dateline' => $_G['timestamp']
			);
			$this->reply->insert($data);
			
			$result = $GLOBALS['db']->query("UPDATE ".tname('businesslog')." SET 
			`replies`=`replies`+1,newreply='".cutstr($reply, 40)."',newreply_dateline='$_G[timestamp]',newreply_user='$_G[username]' WHERE bid='$bid'");
			
			if($result) {
				$msg = '已成功发表回复';
				$code = '1';
			} else {
				$msg = '发表回复失败';
				$code = '-1';
			}
			if($ajax == 1) {
				$arr = array(
					'msg' => $msg,
					'username' => $_G['username'],
					'reply' => cutstr($reply, 40),
					'reply_date' => date("Y-m-d H:i", $_G['timestamp'])
				);
				exit(json_encode($arr));
			} else {
				$_SESSION['message'] = array('code' => $code, 'content' => array($msg));
				header('Location: index.php?home=businesslog&act=view&bid='.$bid);
			}
			
		}
	}
	
}
function business_check($checkdata) {
	$i = 0;
	foreach ($checkdata as $k=>$v) {
		if(mb_strlen(trim($v), 'UTF-8') > 5000) showmessage(lang($k, 'businesslog').'的字数超出限制');
		if($v == "") $i++;
	}
	if($i == count($checkdata)) showmessage('请填写工作日志');
}
?>
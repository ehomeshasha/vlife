<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}
		
class thread_controller {

	//构造函数
	public function __construct() {
		include ROOT_PATH.'./models/common.php';
		$this->forum_thread = new common('forum_thread');
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
		$title = getgpc('title');
		$view = getgpc('view');
		$mention = getgpc('mention');
		$threadtype = getgpc('threadtype'); 
		$userlevel = getgpc('userlevel');
		$score_values = getgpc('score_value');
		
		
		$where = "status>=0";
		$where .= " AND dateline>'{$date['starttime']}' AND dateline<'{$date['endtime']}'";
		$where .= empty($authorid) ? "" : " AND uid='$authorid'";
		$where .= empty($title) ? "" : " AND title LIKE '%$title%'";
		$where .= empty($view) ? "" : " AND uid='$_G[uid]'";
		$where .= $threadtype == "" ? "" : " AND threadtype='$threadtype'";
		$where .= $userlevel == "" ? "" : " AND userlevel='$userlevel'";

		
		
		if($score_values != 0){
			if($score_values > 0){
				$where .= " AND score_value = '$score_values'";
				
			}
		}
		if($score_values == "no"){
				echo " *okok* ";
				$where .= " AND score_value is null";
		}

		
		
		$threadlist_ori = $this->forum_thread->getAll(" AND ".$where, array(), "ORDER BY dateline DESC", "LIMIT $start, $perpage");
		
		if($mention == 'me' || ($_G['userlevel'] != 9 && $_G['userlevel'] != 2)) {
			$threadlistall_ori = $this->forum_thread->getAll(" AND ".$where, array(), "ORDER BY dateline DESC");
			$count = 0;
			foreach ($threadlistall_ori as $val) {
				$arr2 = explode(',', $val['sendlist']);
				if($mention == 'me') {
					if(!in_array($_G['username'], $arr2)) continue;
				} else {
					if(!in_array($_G['username'], $arr2) && $_G['uid'] != $val['uid']) continue;
				}
				$threadlistall[] = $val;
				$count++;
			}
			foreach ($threadlistall as $k=>$v) {
				if($k < $start || $k >= ($start+$perpage)) continue;
				$threadlist[] = $v;
			}
			
		} else {
			$threadlist = $threadlist_ori;
			$count = getcount('forum_thread', $where);	
		}
		$url = "index.php?home=thread";
		$multi = multi($count, $perpage, $page, $url);
		
		//echo $where;
		include template('threadlist');
		
	}
	
	public function post_action() {
		global $_G;
		
		if(!submitcheck('submit')) {
			$cur_location = '发布论坛帖子';
			$userlist_html = get_userlist_html($_G['action']);
			
			include template('thread');
		} else {
			$titleArr = $_POST['title'];
			$linkArr = $_POST['link'];
			$typeArr = $_POST['type'];
			$sendlistArr = $_POST['sendlist_str'];
			
			foreach ($typeArr as $k=>$v) {
				if($titleArr[$k] == '' || $linkArr[$k] == '') {
					
					continue;
				}
				$titleArr2[] = $titleArr[$k];
				$linkArr2[] = $linkArr[$k];
				$sendlist2Arr[] = $sendlistArr[$k];
				$typeArr2[] = $v;
			}
			foreach ($typeArr2 as $key=>$val) {
				$data = array(
					'uid' => $_G['uid'],
					'username' => $_G['username'],
					'userlevel' => $_G['userlevel'],
					'title' => $titleArr2[$key],
					'link' => $linkArr2[$key],
					'threadtype' => $val,
					'sendlist' => $sendlist2Arr[$key],
					'status' => '-2',
					'dateline' => $_G['timestamp']
				);
				$this->forum_thread->insert($data);
			}
			$threadcount = count($typeArr2);
			
			showmessage('您已成功发布'.$threadcount.'条论坛帖子到草稿箱)', 1, 'index.php?home=draft&type=thread');
		}
	}
	
	public function edit_action() {
		global $_G;
		$tid = getgpc('tid');
		$thread = $this->forum_thread->GetOne(" AND tid='$tid'", array('uid','title','link','threadtype','sendlist'));
		$userlist_html = get_userlist_html($_G['action'], $thread['sendlist']);
		
		if(!submitcheck('submit')) {
			
			if($_G['uid'] != $thread['uid'] && $_G['userlevel'] != 9) showerror("你无权进行此操作");
			
			$body = "<label>帖子标题</label><input type='text' class='text span5' name='title' value='{$thread['title']}' maxlength='255'><br />
					<label>帖子链接</label><input type='text' class='text span5' name='link' value='{$thread['link']}' maxlength='255'>
					<label>帖子类型</label><select name='type' class='input-small'>";
			foreach ($_G['ArrayData']['threadtype'] as $k=>$v) {
				$selected = $k == $thread['threadtype'] ? "selected='selected'" : "";
				$body .= "<option value='$k' $selected>$v[name]</option>"; 	
			}
			$body .= "</select><label>@人员列表</label>{$userlist_html}";
			
			$cfm_box = array(
				'input' => array('tid' => $tid, 'submit' => 'true'),
				'action' => 'index.php?home=thread&act=edit',
				'title' => "帖子修改",
				'body' => $body,
				'icon' => "",
				'button1' => "提交",
				'button2' => ""
			);
			include template('confirmbox');
		} else {
			
			if($_G['uid'] != $thread['uid'] && $_G['userlevel'] != 9) showresult("您无权进行此操作");
			
			$tid = getgpc('tid');
			$title = getgpc('title');
			$link = getgpc('link');
			$type = getgpc('type');
			$sendlist = implode(",", getgpc('sendlist'));
			
			$data = array(
				'title' => $title,
				'link' => $link,
				'threadtype' => $type,
				'sendlist' => $sendlist
			);
			$result = $this->forum_thread->UpdateData($data, " AND tid='$tid'");
			
			if($result) {
				$_SESSION['message'] = array('code' => '1', 'content' => array('已成功修改帖子'));
			} else {
				$_SESSION['message'] = array('code' => '-1', 'content' => array('帖子修改失败'));
			}
			
			header("Location: {$_SERVER['HTTP_REFERER']}");
		}
	}
	
	public function score_action() {
		global $_G;
		if($_G['userlevel'] != 9 && $_G['userlevel'] != 2) exit('你无权进行此操作');
		$tid = getgpc('tid');
		$thread = $this->forum_thread->GetOne(" AND tid='$tid'", array('dateline','username','link','title','score_type','score_value','threadtype'));
		/*
		if(!submitcheck('submit')) {
			$thread_type = get_threadtype($thread['threadtype']);
			$thread_title = cutstr($thread['title'], 100);
			$thread_date = date("Y-m-d H:i:j", $thread['dateline']);
			$body = "<div class='control-group'>
						<label class='control-label'>标题</label>
						<div class='controls ptn'>
							<a class='' href='{$thread['link']}' target='_blank'>$thread_title</a>
						</div>
					</div>";
			$body .= "<div class='control-group'>
						<label class='control-label'>类型</label>
						<div class='controls ptn'>
							$thread_type
						</div>
					</div>";
			$body .= "<div class='control-group'>
						<label class='control-label'>发布者</label>
						<div class='controls ptn'>
							{$thread['username']}
						</div>
					</div>";
			$body .= "<div class='control-group'>
						<label class='control-label'>日期</label>
						<div class='controls ptn'>
							$thread_date
						</div>
					</div>";
			$body .= "<div class='control-group'>
						<label class='control-label'>选择分数</label>
						<div class='controls'>";
			$body .= "		<select name='score'>";
			foreach ($_G['ArrayData']['scorelevel'] as $k=>$v) {
				$selected = !empty($thread['score_type']) && $thread['score_type'] == $v ?  "selected='selected'" : "";
				$score_value = $_G['ArrayData']['threadtype'][$thread['threadtype']]['score'][$k];
				$body .= "		<option value='$k' $selected>{$v}({$score_value}分)</option>";
			}
			$body .="			<option value='-1'>取消评分</option>";
			$body .= "		</select>
						</div>
					</div>";
			$cfm_box = array(
				'input' => array('tid' => $tid, 'submit' => 'true'),
				'action' => 'index.php?home=thread&act=score',
				'title' => "打分操作",
				'body' => $body,
				'icon' => "",
				'button1' => "提交",
				'button2' => "",
				'class' => 'form-horizontal'
			);
			include template('confirmbox');
		} else {*/
			$score_level = getgpc('score');
			if($score_level == -1 || $score_level == "") {
				$GLOBALS['db']->query("UPDATE ".tname('forum_thread')." SET `score_type`='',`score_value`=NULL WHERE tid='$tid'");
				//$_SESSION['message'] = array('code' => '1', 'content' => array('已成功取消评分'));
				echo '已成功取消评分';
			} else {
				$score_value = $_G['ArrayData']['threadtype'][$thread['threadtype']]['score'][$score_level];
				$score_alpha = $_G['ArrayData']['scorelevel'][$score_level];
				$this->forum_thread->UpdateData(array('score_type'=>$score_alpha, 'score_value'=>$score_value), " AND tid='$tid'");
				//$_SESSION['message'] = array('code' => '1', 'content' => array('已成功给帖子打分'));
				echo '已成功给帖子打分';
			}
			
			//header("Location: {$_SERVER['HTTP_REFERER']}");
		//}
	}
	public function change_threadtype_action() {
		global $_G;
		if($_G['userlevel'] != 9 && $_G['userlevel'] != 2) exit('你无权进行此操作');
		$tid = getgpc('tid');
		$thread = $this->forum_thread->GetOne(" AND tid='$tid'", array('score_type'));
		$isscore = empty($thread['score_type']) ? 0 : 1;
		$threadtype = getgpc('threadtype');
		$this->forum_thread->UpdateData(array('threadtype'=>$threadtype), " AND tid='$tid'");
		$html = "";
		foreach ($_G['ArrayData']['scorelevel'] as $k=>$v) {
			$ext = $isscore == 1 && $threadtype == $k ? "selected='selected'" : "";
			$html .= "<option value='$k'>{$v}({$_G['ArrayData']['threadtype'][$threadtype]['score'][$k]})</option>";
		}
		$arr = array(
			'msg' => '已成功修改帖子类型',
			'html' => $html,
			'isscore' => $isscore
		);
		echo json_encode($arr);
	}
	
	
	public function delete_action() {
		global $_G;
		$tid = getgpc('tid');
		$authorid = getgpc('authorid');
		
		if($_G['userlevel'] == 9) {
			$str1 = "(论坛帖子将被移送到回收站)";
			$str2 = "(可在回收站恢复)";
		}
		if(!submitcheck('submit')) {
			
			if($_G['uid'] != $authorid && $_G['userlevel'] != 9) showerror("你无权进行此操作");
		
			$cfm_box = array(
				'input' => array('tid' => $tid, 'submit' => 'true', 'authorid' => $authorid),
				'action' => 'index.php?home=thread&act=delete',
				'title' => "删除操作",
				'body' => "确定要删除这条论坛帖子吗？{$str1}",
				'icon' => "icon-question-sign",
				'button1' => "确定",
				'button2' => "不了，谢谢"
			);
			include template('confirmbox');
		} else {
			
			if($_G['uid'] != $authorid && $_G['userlevel'] != 9) showresult("你无权进行此操作");
			if($_G['userlevel'] == 9) {
				$result = $this->forum_thread->UpdateData(array('status'=>'-1'), " AND tid='$tid'");
			} else {
				$result = $this->forum_thread->DeleteData("tid='$tid'");
			}
			
			if($result) {
				$_SESSION['message'] = array('code' => '1', 'content' => array("已成功删除论坛帖子{$str2}"));
			} else {
				$_SESSION['message'] = array('code' => '-1', 'content' => array('论坛帖子删除失败'));
			}
			header("Location: {$_SERVER['HTTP_REFERER']}");
		}
	}
	
	public function view_action() {
		global $_G;
		
		$cur_location = '查看帖子';
		$tid = getgpc('tid');
		$thread = $this->forum_thread->GetOne(" AND tid='$tid'");
		$sendlistArr = explode(",", $thread['sendlist']);
		if(!in_array($_G['username'], $sendlistArr) && $_G['uid'] != $thread['uid'] && $_G['userlevel'] != 9 && $_G['userlevel'] == 2) showresult("帖子只有被@的人才可以查看");	
		if($businesslog['status'] == "-1" && $_G['userlevel'] != '9') showresult("帖子在回收站中,只有管理员可以查看");
		if($businesslog['status'] == "-2" && $_G['uid'] != $thread['uid'] && $_G['userlevel'] != '9') showresult("帖子在草稿箱中,只有作者自己可以查看");
		
		$replies = $this->reply->getAll(" AND fid='$tid' AND status>=0 AND replytype=1", array(), "ORDER BY dateline DESC");
		
		$userlist_html = $thread['sendlist'];
		$this->forum_thread->UpdateData(array('views'=>'views+1'), " AND tid='$tid'", '', '');
		
		include template('thread');
	}
	
	public function reply_action() {
		global $_G;
		$tid = getgpc('tid');
		$ajax = getgpc('ajax');
		if($ajax == 1) {
			$javascript_ajax = "
			var data = $(this).serialize(); 
			$.ajax({
				url:\"index.php?home=thread&act=reply&ajax=1\",
				data:data,
				type:'post',
				dataType:'json',
				error:function(){alert('操作失败，请重新尝试');return false;},
				success:function(data){
					//alert(data);
					$('#response_modal').modal('hide');
					var html = data.reply + \"<br /><span class='mrm' style='font-size:12px;'>\" + data.reply_date + \"</span><span>\" + data.username + \"</span>\";
					$('#replybox_{$tid}').html(html);
					var num = $('#replynum_{$tid}').html();
					$('#replynum_{$tid}').html(parseInt(num)+1);
				}
			});
			return false;";
		}
		if(!submitcheck('submit')) {
			$body = "<textarea class='input_check big_textarea span5' name='reply' id='reply' rows='5'></textarea><span class='help-block'>已输入<strong class='inputnum'>0</strong>字</span>";
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
				'input' => array('tid' => $tid, 'submit' => 'true'),
				'action' => 'index.php?home=thread&act=reply',
				'title' => "发表回复",
				'body' => $body,
				'icon' => "",
				'button1' => "提交",
				'sumbmit_javascript' => $submit_javascript
			);
			include template('confirmbox');
		} else {
			$reply = getgpc('reply');
			$fid = getgpc('fid') ? getgpc('fid') : $tid;
			
			$data = array(
				'fid' => $fid,
				'uid' => $_G['uid'],
				'username' => $_G['username'],
				'userlevel' => $_G['userlevel'],
				'reply' => $reply,
				'replytype' => '1',
				'dateline' => $_G['timestamp']
			);
			$this->reply->insert($data);
			
			$result = $GLOBALS['db']->query("UPDATE ".tname('forum_thread')." SET 
			`replies`=`replies`+1,newreply='".cutstr($reply, 20)."',newreply_dateline='$_G[timestamp]',newreply_user='$_G[username]' WHERE tid='$tid'");
			
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
					'reply' => cutstr($reply, 20),
					'reply_date' => date("Y-m-d H:i", $_G['timestamp'])
				);
				exit(json_encode($arr));
			} else {
				$_SESSION['message'] = array('code' => $code, 'content' => array($msg));
				header('Location: index.php?home=thread&act=view&tid='.$tid);
			}
		}
	}
	
	public function iframe_action() {
		
		$tid = getgpc('tid');
		$thread = $this->forum_thread->GetOne(" AND tid='$tid'", array('link'));
		include template('iframe');
		
	}
}
?>
<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class bulletin_controller {

	public function __construct() {
		include ROOT_PATH.'./models/common.php';
		$this->bulletin = new common('bulletin');
	}
	public function index_action() {
		global $_G;
		
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
		$content = getgpc('content');
		
		
		$where = "status>=0";
		$where .= " AND dateline>'{$date['starttime']}' AND dateline<'{$date['endtime']}'";
		$where .= empty($content) ? "" : " AND `content` LIKE '%$content%'";
		
		$bulletinlist = $this->bulletin->getAll(" AND ".$where, array(), "ORDER BY dateline DESC", "LIMIT $start, $perpage");
		$count = getcount('bulletin', $where);	
		
		$url = "index.php?home=bulletin";
		$multi = multi($count, $perpage, $page, $url);
		//echo $where;
		include template('bulletinlist');
	}
	public function post_action() {
		global $_G;
		
		if(!submitcheck('submit')) {
			if($_G['userlevel'] != 9) showerror('您无权进行此操作');
			
			$body .= "<label>公告标题</label><input type='text' name='title' id='title' maxlength='20' class='span5' />
			<label>公告内容</label><textarea name='content' class='input_check big_textarea span5' row='5' id='content' style='min-height:150px;'></textarea>
			<span class='help-block'>已输入<strong class='inputnum'>0</strong>字</span>";
			$submit_javascript = <<<EOF
<script type="text/javascript">
$(function(){
	$("#modal_form").submit(function(){
		if($("#title").val() == "" || $("#content").val() == "") {
			alert('输入的数据不能为空');
			return false;
		}
	});
});
</script>
EOF;
			$cfm_box = array(
				'input' => array('submit' => 'true'),
				'action' => 'index.php?home=bulletin&act=post',
				'title' => "发布OA公告",
				'body' => $body,
				'icon' => "",
				'button1' => "提交",
				'sumbmit_javascript' => $submit_javascript
			);
			include template('confirmbox');
		} else {
			
			if($_G['userlevel'] != 9) showresult('您无权进行此操作');
			$title = getgpc('title');
			$content = getgpc('content');
			if(!check_blank(array($title, $content))) showresult('输入的数据不能为空');  
			
			
			$data = array(
				'uid' => $_G['uid'],
				'username' => $_G['username'],
				'title' => $title,
				'content' => $content,
				'status' => '0',
				'dateline' => $_G['timestamp']
			);
			
			$result = $this->bulletin->insert($data);
			
			if($result) {
				$_SESSION['message'] = array('code' => '1', 'content' => array('已成功发布OA公告'));
			} else {
				$_SESSION['message'] = array('code' => '-1', 'content' => array('OA公告发布失败'));
			}
			header("Location: {$_SERVER['HTTP_REFERER']}");
		}
	}
	public function edit_action() {
		global $_G;
		$id = getgpc('id');
		
		if(!submitcheck('submit')) {
			
			if($_G['userlevel'] != 9) showerror('您无权进行此操作');
			
			$bulletin = $this->bulletin->GetOne(" AND id='$id'", array('title','content'));
			$len = mb_strlen($bulletin['content'], 'UTF-8');
			$body .= "<label>公告标题</label><input type='text' name='title' id='title' maxlength='20' class='span5' value='{$bulletin['title']}'/>
			<label>公告内容</label><textarea name='content' class='input_check big_textarea span5' row='5' id='content' style='min-height:150px;'>{$bulletin['content']}</textarea>
			<span class='help-block'>已输入<strong class='inputnum'>$len</strong>字</span>";

			$submit_javascript = <<<EOF
<script type="text/javascript">
$(function(){
	$("#modal_form").submit(function(){
		if($("#title").val() == "" || $("#content").val() == "") {
			alert('输入的数据不能为空');
			return false;
		}
	});
});
</script>
EOF;
			
			$cfm_box = array(
				'input' => array('id' => $id, 'submit' => 'true'),
				'action' => 'index.php?home=bulletin&act=edit',
				'title' => "公告修改",
				'body' => $body,
				'icon' => "",
				'button1' => "提交",
				'sumbmit_javascript' => $submit_javascript
			);
			include template('confirmbox');
		} else {
			
			if($_G['userlevel'] != 9) showresult("您无权进行此操作");
			
			$id = getgpc('id');
			$title = getgpc('title');
			$content = getgpc('content');
			
			$data = array(
				'title' => $title,
				'content' => $content
			);
			$result = $this->bulletin->UpdateData($data, " AND id='$id'");
			
			if($result) {
				$_SESSION['message'] = array('code' => '1', 'content' => array('已成功修改OA公告'));
			} else {
				$_SESSION['message'] = array('code' => '-1', 'content' => array('OA公告修改失败'));
			}
			
			header("Location: {$_SERVER['HTTP_REFERER']}");
		}
	}
	public function delete_action() {
		global $_G;
		if($_G['userlevel'] != 9) showresult('你无权进行此操作');
		$id = getgpc('id');
		if(!submitcheck('submit')) {
			$cfm_box = array(
				'input' => array('id' => $id, 'submit' => 'true'),
				'action' => 'index.php?home=bulletin&act=delete',
				'title' => "删除操作",
				'body' => "确定要删除这条OA公告吗？(公告将被移送到回收站)",
				'icon' => "icon-question-sign",
				'button1' => "确定",
				'button2' => "不了，谢谢"
			);
			include template('confirmbox');
		} else {
			$result = $this->bulletin->UpdateData(array('status'=>'-1'), " AND id='$id'");
			if($result) {
				$_SESSION['message'] = array('code' => '1', 'content' => array('已成功删除论坛帖子(可在回收站恢复)'));
			} else {
				$_SESSION['message'] = array('code' => '-1', 'content' => array('论坛帖子删除失败'));
			}
			header("Location: {$_SERVER['HTTP_REFERER']}");
		}
	}
}
?>
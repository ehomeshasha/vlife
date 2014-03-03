<? if(!defined('IN_JZ100')) exit('Access Denied'); include template('header', '0', ''); ?><section>
<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
<ul class="breadcrumb">
  	<li><a href="index.php">首页</a> <span class="divider">/</span></li>
  	<li><a href="<?=$_G['cur_link']?>">论坛帖子(<?=$count?>)</a></li>
</ul>
<?=$_G['message']?>
<div>
<form class="form-inline" action="index.php?home=thread" method="post" id="search_form">
<input type="hidden" name="view" value="<?=$view?>" class="para_for_pagejump" />
<input type="hidden" name="mention" value="<?=$mention?>" class="para_for_pagejump" />
<span class="help-inline">开始时间：</span>
<input class="datepicker datetext" type="text" value="<?=$date['startdate']?>" name="startdate" id="startdate">
<span class="help-inline">结束时间：</span>
  	<input class="datepicker datetext" type="text" value="<?=$date['enddate']?>" name="enddate" id="enddate">
  	<span class="help-inline">帖子类型：</span>
  	<select name="threadtype" class="input-small author-input">
  		<option value="">全部</option>
  <? if(is_array($_G['ArrayData']['threadtype'])) { foreach($_G['ArrayData']['threadtype'] as $t_k => $t_v) { ?>  		<option value="<?=$t_k?>" <? if($t_k == $threadtype && $threadtype != "") { ?>selected="selected"<? } ?>><?=$t_v['name']?></option>
  <? } } ?>  	</select>
  	<span class="help-inline">用户级别：</span>
  	<select name="userlevel" class="input-small author-input">
  		<option value="">全部</option>
  <? if(is_array($_G['ArrayData']['userlevel'])) { foreach($_G['ArrayData']['userlevel'] as $u_k => $u_v) { ?>  		<option value="<?=$u_k?>" <? if($u_k == $userlevel && $userlevel != "") { ?>selected="selected"<? } ?>><?=$u_v?></option>
  <? } } ?>  	</select>
  	<span class="help-inline">作者：</span>
  	<select name="authorid" class="input-small author-input">
  		<option value="0">全部</option>
  <? if(is_array($userlist)) { foreach($userlist as $v) { ?>  		<option value="<?=$v['uid']?>" <? if($v['uid'] == $authorid) { ?>selected="selected"<? } ?>><?=$v['username']?></option>
  		<? } } ?>  	</select>
<span class="help-inline">分数：</span>
  	<select name="score_value" class="input-small author-input">
  		<option value="0">全部</option>
<option value="no">未评分</option>
  <? if(is_array($_G['ArrayData']['score_value'])) { foreach($_G['ArrayData']['score_value'] as $s_k => $s_v) { ?>  		<option value="<?=$s_k?>"><?=$s_v?></option>
  <? } } ?>  	</select>
  	<span class="help-inline">帖子标题：</span>
  	<input type="text" name="title" value="<?=$title?>" maxlength="255" />
  	&nbsp;&nbsp;
  	<button type="submit" class="btn btn-primary search_btn">查找</button>
</form>
</div>
<table class="table table-hover table-bordered">
<tr>

    	<td width="5%">ID</td>
    	<td width="6%">作者</td>
    	<td width="">帖子标题</td>
    	<td width="7%">分数</td>
    	<td width="6%">帖子类型</td>
    	<td width="9%">@的用户</td>
    	<td width="5%">回复</td>
    	<td width="5%">查看</td>
    	<td width="15%">最新回复</td>
    	<td width="10%">发布日期</td>
    	<td width="10%">操作</td>
  	</tr><? if(is_array($threadlist)) { foreach($threadlist as $k => $v) { ?><tr>

    	<td class="tid"><?=$v['tid']?></td>
    	<td><a href="javascript:;" class="username text-error" id="uid_<?=$v['uid']?>"><?=$v['username']?></a></td>
    	<td><a id="view_iframe_<?=$v['tid']?>" class="mrm view_iframe" href="" data-href="index.php?home=thread&amp;act=iframe&amp;tid=<?=$v['tid']?>" data-toggle="modal"><? echo cutstr($v['title'], 100) ?></a><a href="javascript:;" class="text-success view_linkpage" id="view_linkpage_<?=$v['tid']?>" href-attr="<?=$v['link']?>" target="_blank">[详情]</a></td>
    	<td>
    		<? if($_G['userlevel'] == 2 || $_G['userlevel'] == 9) { ?>
    		<select class="score_thread input-small <? if(!empty($v['score_type'])) { ?>bg-green<? } ?>" data-toggle="tooltip" data-trigger="manual" data-title="" data-placement="bottom">
    		<? if(empty($v['score_type'])) { ?>
    		<option value="" class='score_default'>评分</option>
    		<? } ?>
    <? if(is_array($_G['ArrayData']['scorelevel'])) { foreach($_G['ArrayData']['scorelevel'] as $kk => $vv) { ?>    <? $selected = !empty($v['score_type']) && $v['score_type'] == $vv ?  "selected='selected'" : "";
$score_value = $_G['ArrayData']['threadtype'][$v['threadtype']]['score'][$kk];
    		 ?>    		<option value='<?=$kk?>' <?=$selected?>><?=$vv?>(<?=$score_value?>分)</option>
    <? } } ?>    		<? if(!empty($v['score_type'])) { ?>
    		<option value="-1">取消评分</option>
    		<? } ?>
    		</select>
    		<? } else { ?>
    <? echo get_score($v['score_type'], $v['score_value']); ?>    		<? } ?>
    	</td>
    	<td>
    	<? if($_G['userlevel'] == 2 || $_G['userlevel'] == 9) { ?>
    	<select class="change_threadtype input-small" data-toggle="tooltip" data-trigger="manual" data-title="" data-placement="bottom">
    <? if(is_array($_G['ArrayData']['threadtype'])) { foreach($_G['ArrayData']['threadtype'] as $key => $val) { ?>    		<option value="<?=$key?>" <? if($key == $v['threadtype']) { ?>selected="selected"<? } ?>><?=$val['name']?></option><? } } ?></select>
<? } else { ?>
    <? echo get_threadtype($v['threadtype']); ?>    	<? } ?>
    	</td>
    	<td><?=$v['sendlist']?></td>
    	<td id='replynum_<?=$v["tid"]?>'><?=$v['replies']?></td>
    	<td><?=$v['views']?></td>
    	<td id='replybox_<?=$v["tid"]?>'><?=$v['newreply']?><br /><span class="mrm" style="font-size:12px;"><? echo get_date("Y-m-d H:i" ,$v['newreply_dateline']); ?></span><span><?=$v['newreply_user']?></span></td>
    	<td><? echo get_abbr_date($v['dateline'], true, 'Y-m-d'); ?></td>
    	<td>
    		<? if($_G['userlevel'] == 2 || $_G['userlevel'] == 9) { ?>
    		<!--<a data-toggle="modal" data-href="index.php?home=thread&amp;act=score&amp;tid=<?=$v['tid']?>" href="" class="text-error">打分</a>-->
    		<? } ?>
    		<a data-toggle="modal" data-href="index.php?home=thread&amp;act=reply&amp;tid=<?=$v['tid']?>&amp;ajax=1" class="text-success" title="回复数 <?=$v['replies']?>" href="">回复</a>
    		<a href="index.php?home=thread&amp;act=view&amp;tid=<?=$v['tid']?>" title="查看数 <?=$v['views']?>" target="_blank" class="">查看</a>
    		<? if($_G['userlevel'] == 9 || $_G['uid'] == $v['uid']) { ?>
    		<br/>
    		<a data-toggle="modal" data-href="index.php?home=thread&amp;act=edit&amp;tid=<?=$v['tid']?>" href="" class="muted">修改</a>
    		<a data-toggle="modal" data-href="index.php?home=thread&amp;act=delete&amp;tid=<?=$v['tid']?>&amp;authorid=<?=$v['uid']?>" href="" class="muted">删除</a>
    		<? } ?>
    	</td>
  	</tr><? } } ?></table>
<div class="pagination pagination-right"><? include template('perpage', '0', ''); ?><?=$multi?>
</div>
</div>
</div>
</div>
<section><? include template('footer', '0', ''); ?>
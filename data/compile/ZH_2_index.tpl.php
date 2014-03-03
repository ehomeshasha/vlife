<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('header', '0', ''); ?><!--
<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">杂项<b class="caret"></b></a>
<ul class="dropdown-menu">
<li><a href="#">OA管理公告</a></li>
<li><a href="#">人员评分统计</a></li>
</ul>
</li>
-->				
<section>
<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
<ul class="breadcrumb">
  	<li><a href="index.php">首页</a></li>
</ul>
<?=$_G['message']?>
</div>
</div>
</div>
<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
<div class="alert">
<? if($_G['userlevel'] == 9) { ?>
<span class="span2 bref_title mrw">OA公告与个人统计</span>  
<a data-toggle="modal" data-href="index.php?home=bulletin&amp;act=post" class="btn btn-danger" href="">写公告</a>
<? } else { ?>
OA公告与个人统计
<? } ?>
</div>
<div class="row-fluid">
<div class="span4">
<table class="table table-bordered">
<tr class="success">
<td>OA 公告</td>
</tr>
<tr>
<td>
<div>
<h4 style="text-align:center;"><?=$bulletinlist['0']['title']?></h4>
<p style="min-height:198px;padding:0 15px;"><? echo init_textarea($bulletinlist['0']['content']); ?></p>
<div style="text-align:right;"><? echo date("Y-m-d H:i:s", $bulletinlist['0']['dateline']); ?></div>
</div>
</td>
</tr>
</table>
</div>
<div class="span4">
<table class="table table-bordered table-hover">
<tr class="success">
<td>
<span class="z">以往公告</span>
<a href="index.php?home=bulletin" class="y">更多</a>
</td>
</tr><? if(is_array($bulletinlist)) { foreach($bulletinlist as $bulletin) { ?><tr>
<td>
<div class="index_tablerow">
<a href="javascript:;" class="view_bulletin z" title="<?=$bulletin['content']?>"><?=$bulletin['title']?></a>
<span class="y"><? echo date("Y-m-d", $bulletin['dateline']); ?></span>
</div>
<div style="display:none;">
<?=$bulletin['content']?>
&nbsp;&nbsp;<a href="javascript:;" class="hide_bulletin">[收起]</a>
</div>
</td>
</tr><? } } ?></table>
</div>
<div class="span4">
<table class="table table-bordered table-hover">
<tr class="success">
<td colspan="5">
<span class="z">本月个人统计(<?=$statistic_date['startdate']?>-<?=$statistic_date['enddate']?>)</span>
</td>
</tr>
<tr>
<td></td>
<td>数量</td>
<td colspan="2">最新发布(回复)日期</td>
</tr>
<tr>
<td>我的日志</td>
<td><?=$statistic_count['businesslogcount']?></td>
<td colspan="2"><?=$statistic_count['businesslog_date']?></td>
</tr>
<tr>
<td>@我的日志</td>
<td><?=$statistic_count['businesslog_at_count']?></td>
<td colspan="2"><?=$statistic_count['businesslog_at_date']?></td>
</tr>
<tr>
<td>发表日志回复</td>
<td><?=$statistic_count['businesslogreplycount']?></td>
<td colspan="2"><?=$statistic_count['businesslogreply_content']?>&nbsp;&nbsp;<?=$statistic_count['businesslogreply_date']?></td>
</tr>
<tr>
<td>我的帖子</td>
<td><?=$statistic_count['threadcount']?></td>
<td colspan="2"><?=$statistic_count['thread_date']?></td>
</tr>
<tr>
<td>@我的帖子</td>
<td><?=$statistic_count['thread_at_count']?></td>
<td colspan="2"><?=$statistic_count['thread_at_date']?></td>
</tr>
<tr>
<td>发表帖子回复</td>
<td><?=$statistic_count['threadreplycount']?></td>
<td colspan="2"><?=$statistic_count['threadreply_content']?>&nbsp;&nbsp;<?=$statistic_count['threadreply_date']?></td>
</tr>
<tr>
<td>帖子总分</td>
<td><?=$statistics['total']?></td>
<td>帖子平均分</td>
<td><? echo format_score($statistics['average']) ?></td>
</tr>
</table>
</div>
</div>
<div class="alert">
<span class="span2 bref_title mrw">工作日志概况 一览</span>
<a href="index.php?home=businesslog&amp;act=post" class="btn btn-danger">写日志</a>
</div>
<div class="row-fluid">
<div class="span3">
<table class="table table-bordered table-hover">
<tr class="success">
<td>
<span class="z">我的日志</span>
<a href="index.php?home=businesslog&amp;view=me" class="y">更多</a>
</td>
</tr><? if(is_array($businesslog_viewmelist)) { foreach($businesslog_viewmelist as $businesslog_viewme) { ?><tr>
<td>
<div class="index_tablerow">
<? if(!empty($businesslog_viewme)) { ?>
<a class="z index_title" href="index.php?home=businesslog&amp;act=view&amp;bid=<?=$businesslog_viewme['bid']?>"><? echo cutstr($businesslog_viewme['todayplan'], 23); ?></a>
<span class="y index_date" title="<? echo date("Y-m-d H:i:s", $businesslog_viewme['dateline']); ?>"><? echo get_abbr_date($businesslog_viewme['dateline']); ?></span>
<? } ?>
</div>
</td>
</tr><? } } ?></table>
</div>
<div class="span3">
<table class="table table-bordered table-hover">
<tr class="success">
<td>
<span class="z">@我的日志</span>
<a href="index.php?home=businesslog&amp;mention=me" class="y">更多</a>
</td>
</tr><? if(is_array($businesslog_mentionmelist)) { foreach($businesslog_mentionmelist as $businesslog_mentionme) { ?><tr>
<td>
<div class="index_tablerow">
<? if(!empty($businesslog_mentionme)) { ?>
<a class="z index_title" href="index.php?home=businesslog&amp;act=view&amp;bid=<?=$businesslog_mentionme['bid']?>"><? echo cutstr($businesslog_mentionme['todayplan'], 23); ?></a>
<span class="y index_date" title="<? echo date("Y-m-d H:i:s", $businesslog_mentionme['dateline']); ?>"><? echo get_abbr_date($businesslog_mentionme['dateline']); ?></span>
<span class="y mrm text-error"><?=$businesslog_mentionme['username']?></span>
<? } ?>
</div>
</td>
</tr><? } } ?></table>
</div>
<div class="span3">
<table class="table table-bordered table-hover">
<tr class="success">
<td>
<span class="z">我发表的回复</span>
</td>
</tr><? if(is_array($businesslogreplylist)) { foreach($businesslogreplylist as $businesslogreply) { ?><tr>
<td>
<div class="index_tablerow">
<? if(!empty($businesslogreply)) { ?>
<a class="z index_title" href="index.php?home=businesslog&amp;act=view&amp;bid=<?=$businesslogreply['fid']?>"><? echo cutstr($businesslogreply['reply'], 23); ?></a>
<span class="y index_date" title="<? echo date("Y-m-d H:i:s", $businesslogreply['dateline']); ?>"><? echo get_abbr_date($businesslogreply['dateline']); ?></span>
<? } ?>
</div>
</td>
</tr><? } } ?></table>
</div>
<div class="span3">
<table class="table table-bordered table-hover">
<tr class="success">
<td>
<span class="z">别人给我的回复</span>
</td>
</tr><? if(is_array($businesslogreplytomelist)) { foreach($businesslogreplytomelist as $businesslogreplytome) { ?><tr>
<td>
<div class="index_tablerow">
<? if(!empty($businesslogreplytome)) { ?>
<a class="z index_title" href="index.php?home=businesslog&amp;act=view&amp;bid=<?=$businesslogreplytome['fid']?>"><? echo cutstr($businesslogreplytome['reply'], 23); ?></a>
<span class="y index_date" title="<? echo date("Y-m-d H:i:s", $businesslogreplytome['dateline']); ?>"><? echo get_abbr_date($businesslogreplytome['dateline']); ?></span>
<span class="y mrm text-error"><?=$businesslogreplytome['username']?></span>
<? } ?>
</div>
</td>
</tr><? } } ?></table>
</div>
</div>
<div class="alert">
<span class="span2 bref_title mrw">帖子概况一览</span>
<a href="index.php?home=thread&amp;act=post" class="btn btn-danger">发帖子</a>
</div>
<div class="row-fluid">
<div class="span3">
<table class="table table-bordered table-hover">
<tr class="success">
<td>
<span class="z">我的帖子</span>
<a href="index.php?home=thread&amp;view=me" class="y">更多</a>
</td><? if(is_array($thread_viewmelist)) { foreach($thread_viewmelist as $thread_viewme) { ?><tr>
<td>
<div class="index_tablerow">
<? if(!empty($thread_viewme)) { ?>
<a class="z index_title" href="index.php?home=thread&amp;act=view&amp;tid=<?=$thread_viewme['tid']?>"><? echo cutstr($thread_viewme['title'], 23); ?></a>
<span class="y index_date" title="<? echo date("Y-m-d H:i:s", $thread_viewme['dateline']); ?>"><? echo get_abbr_date($thread_viewme['dateline']); ?></span>
<? } ?>
</div>
</td>
</tr><? } } ?></tr>
</table>
</div>
<div class="span3">
<table class="table table-bordered table-hover">
<tr class="success">
<td>
<span class="z">@我的帖子</span>
<a href="index.php?home=thread&amp;mention=me" class="y">更多</a>
</td>
</tr><? if(is_array($thread_mentionmelist)) { foreach($thread_mentionmelist as $thread_mentionme) { ?><tr>
<td>
<div class="index_tablerow">
<? if(!empty($thread_mentionme)) { ?>
<a class="z index_title" href="index.php?home=thread&amp;act=view&amp;tid=<?=$thread_mentionme['tid']?>"><? echo cutstr($thread_mentionme['title'], 23); ?></a>
<span class="y index_date" title="<? echo date("Y-m-d H:i:s", $thread_mentionme['dateline']); ?>"><? echo get_abbr_date($thread_mentionme['dateline']); ?></span>
<span class="y mrm text-error"><?=$thread_mentionme['username']?></span>
<? } ?>
</div>
</td>
</tr><? } } ?></table>
</div>
<div class="span3">
<table class="table table-bordered table-hover">
<tr class="success">
<td>
<span class="z">我发表的回复</span>
</td>
</tr><? if(is_array($threadreplylist)) { foreach($threadreplylist as $threadreply) { ?><tr>
<td>
<div class="index_tablerow">
<? if(!empty($threadreply)) { ?>
<a class="z index_title" href="index.php?home=thread&amp;act=view&amp;tid=<?=$threadreply['fid']?>"><? echo cutstr($threadreply['reply'], 23); ?></a>
<span class="y index_date" title="<? echo date("Y-m-d H:i:s", $threadreply['dateline']); ?>"><? echo get_abbr_date($threadreply['dateline']); ?></span>
<? } ?>
</div>
</td>
</tr><? } } ?></table>
</div>
<div class="span3">
<table class="table table-bordered table-hover">
<tr class="success">
<td>
<span class="z">别人给我的回复</span>
</td>
</tr><? if(is_array($threadreplytomelist)) { foreach($threadreplytomelist as $threadreplytome) { ?><tr>
<td>
<div class="index_tablerow">
<? if(!empty($threadreplytome)) { ?>
<a class="z index_title" href="index.php?home=thread&amp;act=view&amp;tid=<?=$threadreplytome['fid']?>"><? echo cutstr($threadreplytome['reply'], 23); ?></a>
<span class="y index_date" title="<? echo date("Y-m-d H:i:s", $threadreplytome['dateline']); ?>"><? echo get_abbr_date($threadreplytome['dateline']); ?></span>
<span class="y mrm text-error"><?=$threadreplytome['username']?></span>
<? } ?>
</div>
</td>
</tr><? } } ?></table>
</div>
</div>
</div>
</div>
</div>
</section><? include template('footer', '0', ''); ?>
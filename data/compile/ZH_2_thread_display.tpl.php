<? if(!defined('IN_JZ100')) exit('Access Denied'); ?>
<div class="row-fluid">
<div class="span12">
<p class="post_info">
<span>发布人：<?=$thread['username']?></span><span>发布日期：<? echo get_abbr_date($thread['dateline'], true); ?></span>
<span>查看数：<?=$thread['views']?></span><span>回复数：<?=$thread['replies']?></span>
</p>
</div>		
</div>
<div class="row-fluid">
<div class="span12">
<div class="span8">
<ul class="nav nav-tabs mbm">
<li class="active"><a href="#">帖子详情</a></li>
</ul>
        <div id="thread_body">
<div class="well form-horizontal threadblock" id="threadblock">
<div class="control-group">
    	<label class="control-label">帖子标题</label>
    <div class="controls ptn">
    	<?=$thread['title']?>
    </div>
  	</div>
  	<div class="control-group">
    	<label class="control-label">帖子链接</label>
    	<div class="controls ptn">
    		<a href="<?=$thread['link']?>" target="_blank" style="display:block;word-wrap:break-word;"><?=$thread['link']?></a>
    	</div>
 	</div>
  	<div class="control-group">
    	<label class="control-label">帖子类型</label>
    	<div class="controls ptn">
    <? echo get_threadtype($thread['threadtype']); ?>    	 </div>
  	</div>
<div class="control-group">
    	<label class="control-label">@人员列表</label>
    	<div class="controls memberlist ptn">
    		<?=$userlist_html?>
    	</div>
 	</div>
 	<div class="control-group">
    	<label class="control-label">帖子分值</label>
    	<div class="controls ptn">
    		<? if(!empty($thread['score_type'])) { ?>
    <? echo get_score($thread['score_type'], $thread['score_value']); ?>    		<? } else { ?>
    		尚未评分
    		<? } ?>
    	 </div>
  	</div>
  	<div style="text-align:right;">
 		<? if($_G['userlevel'] == 2 || $_G['userlevel'] == 9) { ?>
<a data-toggle="modal" data-href="index.php?home=thread&amp;act=score&amp;tid=<?=$thread['tid']?>" href="" class="btn btn-primary btn-large">打分</a>
<? } ?>
 	</div>
</div>
</div>
        </div>
        <div class="span4">
        	<ul class="nav nav-tabs" style="margin-bottom:10px;">
<li class="active"><a href="#">回复区</a></li>
        </ul>
        <? if(!empty($replies)) { ?>
    	<style type="text/css">
.popover.bottom .arrow:after {border-bottom-color:#f7f7f7;}
.popover-content {word-break:break-word;}
</style>    
<div class="alert alert-success fade in">
            	<span class="mrw">已有<? echo count($replies); ?>人进行了回复</span>
            	<a data-toggle="modal" data-href="index.php?home=thread&amp;act=reply&amp;tid=<?=$thread['tid']?>" class="btn btn-success" href="">添加新回复</a>
            </div>
        <? if(is_array($replies)) { foreach($replies as $reply) { ?>        <span style="display:block;padding-left:20px;margin-top:20px;" data-html="true" id="username_<?=$reply['id']?>" class="username_popover" data-title="<? echo date("Y-m-d H:i:s", $reply['dateline']); ?>" data-content="<? echo get_huanhang($reply['reply']) ?>" data-placement="bottom" data-template="&lt;div style=&quot;max-width:none;position:relative;padding:0px;&quot; class=&quot;popover&quot;&gt;&lt;div style=&quot;left:9%&quot; class=&quot;arrow&quot;&gt;&lt;/div&gt;&lt;h3 class=&quot;popover-title&quot;&gt;&lt;/h3&gt;&lt;div class=&quot;popover-content&quot;&gt;&lt;/div&gt;&lt;/div&gt;"><i class="<? echo geticon($reply['userlevel']) ?>"></i><?=$reply['username']?></span>
        <? } } ?>        <? } else { ?>
        <div class="alert alert-block alert-error fade in">
            	<p>该日志还无人回复，你可以点击下面的按钮进行回复哦</p>
            	<p>
              		<a data-toggle="modal" data-href="index.php?home=thread&amp;act=reply&amp;tid=<?=$thread['tid']?>" class="btn btn-danger" href="">写回复</a>
            	</p>
          	</div>
        <? } ?>
</div>
</div>
</div>

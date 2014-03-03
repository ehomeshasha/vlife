<? if(!defined('IN_JZ100')) exit('Access Denied'); ?>
<div class="row-fluid">
<div class="span12">
<p class="post_info">
<span>发布人：<?=$businesslog['username']?></span><span>发布日期：<? echo get_abbr_date($businesslog['dateline'], true); ?></span>
<span>查看数：<?=$businesslog['views']?></span><span>回复数：<?=$businesslog['replies']?></span>
</p>
</div>		
</div>
<div class="row-fluid">
<div class="span12">
<div class="span8 businesslog_area">
<ul class="nav nav-tabs">
<li class="active"><a href="#">今日工作</a></li>
        </ul>
        <p><? echo init_textarea($businesslog['todayplan']); ?></p>
        <ul class="nav nav-tabs">
<li class="active"><a href="#">完成情况</a></li>
</ul>
<p><? echo init_textarea($businesslog['fulfil']); ?></p>
<ul class="nav nav-tabs">
<li class="active"><a href="#">经验总结</a></li>
        </ul>
        <p><? echo init_textarea($businesslog['summary']); ?></p>
        <ul class="nav nav-tabs">
<li class="active"><a href="#">明日计划</a></li>
        </ul>
        <p><? echo init_textarea($businesslog['tomorrowplan']); ?></p>
        <ul class="nav nav-tabs">
<li class="active"><a href="#">@人员列表</a></li>
           	</ul>
           	<div class="mbw memberlist">
<?=$userlist_html?>
</div>
<ul class="nav nav-tabs">
<li class="active"><a href="#">补充文档(支持图片,doc,excel,和pdf)</a></li>
        	</ul>
        	<? if($businesslog['filepath'] != "") { ?>
       		<label>已上传的文件：</label>
       		<? } ?>
       		<div><? echo get_uploadfile($businesslog['filepath']); ?></div>
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
            	<a data-toggle="modal" data-href="index.php?home=businesslog&amp;act=reply&amp;bid=<?=$businesslog['bid']?>" class="btn btn-success" href="">添加新回复</a>
            </div>
        <? if(is_array($replies)) { foreach($replies as $reply) { ?>        <span style="display:block;padding-left:20px;margin-top:20px;" data-html="true" id="username_<?=$reply['id']?>" class="username_popover" data-title="<? echo date("Y-m-d H:i:s", $reply['dateline']); ?>" data-content="<? echo get_huanhang($reply['reply']) ?>" data-placement="bottom" data-template="&lt;div style=&quot;max-width:none;position:relative;padding:0px;&quot; class=&quot;popover&quot;&gt;&lt;div style=&quot;left:9%&quot; class=&quot;arrow&quot;&gt;&lt;/div&gt;&lt;h3 class=&quot;popover-title&quot;&gt;&lt;/h3&gt;&lt;div class=&quot;popover-content&quot;&gt;&lt;/div&gt;&lt;/div&gt;"><i class="<? echo geticon($reply['userlevel']) ?>"></i><?=$reply['username']?></span>
        <? } } ?>        <? } else { ?>
        <div class="alert alert-block alert-error fade in">
            	<p>该日志还无人回复，你可以点击下面的按钮进行回复哦</p>
            	<p>
              		<a data-toggle="modal" data-href="index.php?home=businesslog&amp;act=reply&amp;bid=<?=$businesslog['bid']?>" class="btn btn-danger" href="">写回复</a>
            	</p>
          	</div>
        <? } ?>
</div>
</div>
</div>

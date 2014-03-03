<? if(!defined('IN_JZ100')) exit('Access Denied'); ?>
<div class="row-fluid">
<div class="span12">
<p class="post_info">
<span>发布人：<?=$_G['username']?></span><span>发布日期：<? echo date("Y-m-d", $_G['timestamp']); ?></span>
</p>
<form class="form-inline" action="index.php?home=thread&amp;act=post" method="post" id="form" onsubmit="setSendlistValue();">
<input type="hidden" name="submit" value="true" />
<div id="thread_body">
<div class="well form-horizontal threadblock" id="threadblock">
<input type="hidden" name="sendlist_str[]" value="" class="sendlist_str" />
<div class="control-group">
    	<label class="control-label">帖子标题</label>
    <div class="controls">
    	<input type="text" class="input-xxlarge" name="title[]" maxlength="255" value="" />
    </div>
  	</div>
  	<div class="control-group">
    	<label class="control-label">帖子链接</label>
    	<div class="controls">
    		<input type="text" class="input-xxlarge" name="link[]" maxlength="255" value="" />
    	</div>
 	</div>
  	<div class="control-group">
    	<label class="control-label">帖子类型</label>
    	<div class="controls">
<select name="type[]" class="input-small"><? if(is_array($_G['ArrayData']['threadtype'])) { foreach($_G['ArrayData']['threadtype'] as $k => $v) { ?><option value="<?=$k?>"><?=$v['name']?></option><? } } ?></select>			    
    	</div>
  	</div>
<div class="control-group">
    	<label class="control-label">@人员列表</label>
    	<div class="controls memberlist">
    		<?=$userlist_html?>
    	</div>
 	</div>
</div>
</div>
<div style="padding-left:8px;">
<a href="javascript:;" class="add_opt">添加新帖子</a>
<div class="divide-line"></div>
<p class="mtm"><button class="btn btn-primary">发布</button></p>
</div>
</form>
</div>
</div>
<? if(!defined('IN_JZ100')) exit('Access Denied'); include template('header', '0', ''); ?><section>
<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
<ul class="breadcrumb">
  	<li><a href="index.php">首页</a> <span class="divider">/</span></li>
  	<li><a href="#">个人中心</a> <span class="divider">/</span></li>
  	<li><a href="<?=$_G['cur_link']?>">草稿箱(<?=$draftcount?>)</a></li>
</ul>
<?=$_G['message']?>
<ul class="nav nav-tabs">
<li <?=$log_active?>><a href="index.php?home=draft&amp;type=log">工作日志(<?=$businesslogcount?>)</a></li>
<li <?=$thread_active?>><a href="index.php?home=draft&amp;type=thread">论坛帖子(<?=$threadcount?>)</a></li>
        	</ul>
        	<p><?=$title?></p>
        	<form action="index.php?home=draft&amp;act=bulk_post&amp;type=<?=$type?>" method="post">
        	<table class="table table-condensed">
        		<tr>
        			<td width="2%"><input type="checkbox" class="check_all input-checkbox-table" isChecked="false" /></td>
        			<td width="5%">ID</td>
        			<td width="10%">作者</td>
        			<td>内容</td>
        			<td width="12%">发布日期</td>
        			<td width="12%">操作</td>
        		</tr>
        <? if(is_array($datelist)) { foreach($datelist as $v) { ?>        	<tr>
        		<td><input type="checkbox" name="id[]" value="<?=$v['id']?>" class="check_tid input-checkbox-table" /></td>
        		<td><?=$v['id']?></td>
        		<td><?=$v['username']?></td>
        		<td><?=$v['content']?></td>
        		<td><? echo date("Y-m-d H:i:s", $v['dateline']); ?></td>
        		<td>
        			<? if($type == 'log') { ?>
        			<a href="<?=$editlink?><?=$v['id']?>">修改</a>
        			<? } elseif($type == 'thread') { ?>
        			<a data-toggle="modal" data-href="index.php?home=thread&amp;act=edit&amp;tid=<?=$v['id']?>" href="">修改</a>
        			<? } ?>
        			<a data-toggle="modal" data-href="index.php?home=draft&amp;act=post&amp;id=<?=$v['id']?>&amp;type=<?=$type?>" href="">发布</a>
        			<a data-toggle="modal" data-href="index.php?home=draft&amp;act=remove&amp;id=<?=$v['id']?>&amp;type=<?=$type?>" href="">删除</a>
</td>
        	</tr>
        	<? } } ?>        	</table>
        	<div>
        		<button class="btn btn-primary" type="submit">全部发布</button>
        	</div>
        	</form>
        	<div class="pagination pagination-right"><? include template('perpage', '0', ''); ?><?=$multi?>
</div>
        </div>
</div>
</div>
</section><? include template('footer', '0', ''); ?>
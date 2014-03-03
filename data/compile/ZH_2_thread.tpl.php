<? if(!defined('IN_JZ100')) exit('Access Denied'); include template('header', '0', ''); ?><section>
<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
<ul class="breadcrumb">
  	<li><a href="index.php">首页</a> <span class="divider">/</span></li>
  	<li><a href="index.php?home=thread">论坛帖子</a> <span class="divider">/</span></li>
  	<li><a href="<?=$_G['cur_link']?><? if($_G['action'] != 'post') { ?>&tid=<?=$thread['tid']?><? } ?>"><?=$cur_location?></a></li>
</ul>
<?=$_G['message']?>
</div>
</div>
<? if($_G['action'] == 'view') { include template('thread_display', '0', ''); } elseif($_G['action'] == 'post') { include template('thread_post', '0', ''); } elseif($_G['action'] == 'edit') { include template('thread_edit', '0', ''); } ?>
</div>
</section><? include template('footer', '0', ''); ?>
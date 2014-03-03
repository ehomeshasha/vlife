<? if(!defined('IN_JZ100')) exit('Access Denied'); include template('header', '0', ''); ?><section>
<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
<ul class="breadcrumb">
  	<li><a href="index.php">首页</a> <span class="divider">/</span></li>
  	<li><a href="index.php?home=businesslog">工作日志</a> <span class="divider">/</span></li>
  	<li><a href="<?=$_G['cur_link']?><? if($_G['action'] != 'post') { ?>&bid=<?=$businesslog['bid']?><? } ?>"><?=$cur_location?></a></li>
</ul>
<?=$_G['message']?>
</div>
</div>
<? if($_G['action'] == 'view') { include template('businesslog_display', '0', ''); } else { include template('businesslog_post', '0', ''); } ?>
</div>
</section><? include template('footer', '0', ''); ?>
<? if(!defined('IN_JZ100')) exit('Access Denied'); include template('header', '0', ''); ?><section>
<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
<ul class="breadcrumb">
  	<li><a href="index.php">首页</a> <span class="divider">/</span></li>
  	<li class="active">操作结果</li>
</ul>
<div class="alert <?=$alert_type?>">
<span class="mrw"><?=$result_body?></span><button class="btn <?=$button_type?>" onclick="history.go(-1);">返回上一页</button>
</div>
</div>
</div>
</div>
</section><? include template('footer', '0', ''); ?>
<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('admin#admin_header', '0', ''); ?><div class="tab-content"><? include template('breadcrumb', '0', ''); ?><?=$_G['message']?>

<div>
<h3>Name: <?=$beacon['name']?></h3>
<h4>UUID: <?=$beacon['uuid']?></h4>
<h4>Restaurant: <?=$company_name?></h4>
<h5>CreateTime: <? echo get_abbr_date($beacon['dateline'], true); ?></h5><? if(is_array($dish_list)) { foreach($dish_list as $v) { ?><div class="well well-small">
<div class="mbm">
<a href="index.php?home=foodorder_dishes&amp;act=post&amp;opt=edit&amp;id=<?=$v['id']?>" title="<?=$v['name']?>">
<img src="<?=$_G['siteurl']?><?=$v['path']?>" style="width:260px;height:195px" title="<?=$v['alt']?>" alt="<?=$v['alt']?>" />
</a>
</div>
<div class="xs3 xw1 clearfix" style="width:260px;">
<span class="mrm pull-left"><?=$v['name']?></span>
<span class="pull-right"><?=$v['price']?></span>
</div>
</div><? } } ?><div>
</div>
</div></div></div></div><? include template('footer', '0', ''); ?>
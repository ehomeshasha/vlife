<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('admin#header', '0', ''); ?><div class="tab-content"><? include template('breadcrumb', '0', ''); ?><?=$_G['message']?>

<div><? if(is_array($dishes)) { foreach($dishes as $v) { ?><div class="well well-small">
<div class="mbm">
<a href="index.php?home=foodorder_dishes&amp;act=post&amp;opt=edit&amp;id=<?=$v['id']?>">
<img src="<?=$_G['siteurl']?><?=$v['path']?>" style="width:260px;height:195px" />
</a>
</div>
<div class="xs3 xw1">
<span><?=$v['name']?></span>
<span><?=$v['food_count']?></span>
<span><?=$v['food_totalprice']?></span>
</div>
</div><? } } ?><div>
<div class="well well-small">
<div>
<strong>
OrderID: <?=$order['order_id']?>			
</strong>
</div>
<div>
<strong>
Phone: <?=$order['phone']?>			
</strong>
</div>
<div>
<strong>
Address: <?=$order['address']?>			
</strong>
</div>
<div>
<strong>
Createtime: <? echo get_abbr_date($order['dateline'], true); ?></strong>
</div>
</div>
</div>
</div></div></div></div><? include template('footer', '0', ''); ?>
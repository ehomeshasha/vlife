<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('admin#header', '0', ''); ?><div class="tab-content"><? include template('breadcrumb', '0', ''); ?><?=$_G['message']?>
<table class="table table-condensed">
<tr>
<td width="">ID</td>
<td width="">Category</td>
    	<td width="">Name</td>
    	<td width="">Price</td>
    	<td width="">Displayorder</td>
    	<td width="">Createtime</td>
    	<td width="">Opration</td>
  	</tr><? if(is_array($dishes)) { foreach($dishes as $k => $v) { ?><tr>

    	<td><?=$v['id']?></td>
    	<td><?=$_G['category'][$v['cid']]['name']?></td>
    	<td><?=$v['name']?></td>
    	<td><?=$v['price']?></td>
    	<td><?=$v['displayorder']?></td>
    	<td><? echo get_abbr_date($v['createtime'], true); ?></td>
    	<td>
    		<a href="index.php?home=foodorder_dishes&amp;act=post&amp;opt=edit&amp;id=<?=$v['id']?>" href="">Edit</a>
    		<a href="javascript:;" class="deletelink" data-id="<?=$v['id']?>" data-type="Dish" data-href="index.php?home=foodorder_dishes&amp;act=delete">Delete</a>
    	</td>
    </tr><? } } ?></table>
<? if(!$_G['mobile']) { ?>
<div class="pagination pagination-right"><? include template('perpage', '0', ''); ?><?=$multi?>
</div>
<? } ?>
</div>
</div></div></div></div><? include template('footer', '0', ''); ?>
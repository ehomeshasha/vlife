<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('admin#admin_header', '0', ''); ?><div class="tab-content"><? include template('breadcrumb', '0', ''); ?><?=$_G['message']?>
<table class="table table-condensed">
<tr>
<td width="">ID</td>
<td width="">Name</td>
<td width="">UUID</td>
<td width="">Restaurant</td>
<td width="">Recommend Dishes</td>
<td width="">Createtime</td>
    	<td width="">Opration</td>
  	</tr><? if(is_array($beacon_list)) { foreach($beacon_list as $v) { ?><tr>

    	<td><?=$v['id']?></td>
    	<td><?=$v['name']?></td>
    	<td><?=$v['uuid']?></td>
    	<td>
    		<span class="mrm"><? echo count(explode(",", $v['dish_ids'])); ?> dishes</span>
    		<a href="index.php?home=foodorder_beacon&amp;act=view&amp;id=<?=$v['id']?>">Detail</a>
    	</td>
    	<td><? echo get_abbr_date($v['dateline'], true); ?></td>
    	<td>
    		<a href="index.php?home=foodorder_beacon&amp;act=post&amp;opt=edit&amp;id=<?=$v['id']?>">Edit</a>
    		<a href="javascript:;" class="deletelink" data-uid="<?=$v['uid']?>" data-id="<?=$v['id']?>" data-type="Beacon" data-href="index.php?home=foodorder_beacon&amp;act=delete">Delete</a>
    	</td>
    </tr><? } } ?></table>
</div>
</div></div></div></div><? include template('footer', '0', ''); ?>
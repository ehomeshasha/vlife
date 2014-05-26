<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('admin#admin_header', '0', ''); ?><div class="tab-content"><? include template('breadcrumb', '0', ''); ?><?=$_G['message']?>
<table class="table table-condensed">
<tr>
<td width="">ID</td>
<td width="">Name</td>
<td width="">Weixin</td>
<? if(!$_G['mobile']) { ?>
<td width="">Phone</td>
<td width="">Address</td>
<? } ?>
    	<td width="">Createtime</td>
    	<td width="">Opration</td>
  	</tr><? if(is_array($_G['company_list'])) { foreach($_G['company_list'] as $k => $v) { ?><tr>

    	<td><?=$v['id']?></td>
    	<td><?=$v['name']?></td>
    	<td><?=$v['weixin']?></td>
    	<? if(!$_G['mobile']) { ?>
    	<td><?=$v['phone']?></td>
    	<td><?=$v['address']?></td>
    	<? } ?>
    	<td><? echo get_abbr_date($v['dateline'], true); ?></td>
    	<td>
    		<a href="index.php?home=foodorder_company&amp;act=post&amp;opt=edit&amp;id=<?=$v['id']?>" href="">Edit</a>
    		<a href="javascript:;" class="deletelink" data-uid="<?=$v['uid']?>" data-id="<?=$v['id']?>" data-type="Restaurant" data-href="index.php?home=foodorder_company&amp;act=delete">Delete</a>
    	</td>
    </tr><? } } ?></table>
</div>
</div></div></div></div><? include template('footer', '0', ''); ?>
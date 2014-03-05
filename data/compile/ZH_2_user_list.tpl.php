<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('superadmin#header', '0', ''); ?><div class="tab-content"><? include template('breadcrumb', '0', ''); ?><?=$_G['message']?>
<ul class="nav nav-tabs">
<li class="<?=$company_active?>"><a href="index.php?home=user&amp;userlevel=<?=$_G['setting']['userlevel']['company']?>">CompanyUser(<?=$company_count?>)</a></li>
<li class="<?=$custom_active?>"><a href="index.php?home=user&amp;userlevel=<?=$_G['setting']['userlevel']['custom']?>">CustomUser(<?=$custom_count?>)</a></li>
      	</ul>
<table class="table table-condensed">
<tr>
<td width="">ID</td>
    	<td width="">Username</td>
    	<td width="">Userlevel</td>
    	<td width="">Register Date</td>
    	<td width="">Opration</td>
  	</tr><? if(is_array($user_list)) { foreach($user_list as $k => $v) { ?><tr>

    	<td><?=$v['uid']?></td>
    	<td><?=$v['username']?></td>
    	<td><?=$v['userlevel']?></td>
    	<td><? echo get_abbr_date($v['dateline'], true); ?></td>
    	<td>
    		<a href="index.php?home=user&amp;act=post&amp;opt=edit&amp;uid=<?=$v['uid']?>" href="">Edit</a>
    		<a href="javascript:;" class="deletelink" data-id="uid#<?=$v['uid']?>" data-type="User" data-href="index.php?home=user&amp;act=delete">Delete</a>
    	</td>
    </tr><? } } ?></table>
<? if(!$_G['mobile']) { ?>
<div class="pagination pagination-right"><? include template('perpage', '0', ''); ?><?=$multi?>
</div>
<? } ?>
</div>
</div></div></div></div><? include template('footer', '0', ''); ?>
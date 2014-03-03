<? if(!defined('IN_SYSTEM')) exit('Access Denied'); ?>
<ul class="breadcrumb"><? if(is_array($breadcrumb)) { foreach($breadcrumb as $bk => $bc) { if(empty($bc['href'])) { ?>
<li class="active"><?=$bc['text']?> <? if($bk != count($breadcrumb)-1) { ?><span class="divider">/</span><? } ?></li>
<? } else { ?>
<li><a href="<?=$bc['href']?>" 
<? if(!empty($bc['is_label'])) { ?>class="addicon label label-<?=$bc['label_type']?>"
data-toggle="tooltip" data-trigger="hover" data-placement="right" 
data-original-title="<?=$bc['tooltip']?>"<? } ?>><?=$bc['text']?></a> <? if($bk != count($breadcrumb)-1) { ?><span class="divider">/</span><? } ?></li>
<? } } } ?></ul>
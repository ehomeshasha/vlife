<? if(!defined('IN_SYSTEM')) exit('Access Denied'); ?>
<?=$cfm_box['sumbmit_javascript']?>
<form class="<?=$cfm_box['class']?>" action="<?=$cfm_box['action']?>" id="modal_form" method="post" style="margin-bottom:0;" onsubmit="$(this).find('input:checkbox').removeAttr('disabled');">
<div class="modal-header">
<a class="close" data-dismiss="modal">&times;</a>
<h3><?=$cfm_box['title']?></h3>
</div>
<div class="modal-body"><? if(is_array($cfm_box['input'])) { foreach($cfm_box['input'] as $k=>$v) { ?><input type="hidden" value="<?=$v?>" name="<?=$k?>" /><? } } ?><p><? if(!empty($cfm_box['icon'])) { ?><i class="<?=$cfm_box['icon']?> mrn"></i><? } ?><?=$cfm_box['body']?></p>
</div>
<div class="modal-footer">
<? if(!empty($cfm_box['button1'])) { ?>
<button class="btn btn-primary" type="submit"><?=$cfm_box['button1']?></button>
<? } if(!empty($cfm_box['button2'])) { ?>
<a class="btn" data-dismiss="modal"><?=$cfm_box['button2']?></a>
<? } ?>
</div>
</form>

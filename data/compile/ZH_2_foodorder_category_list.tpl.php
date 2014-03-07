<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('admin#admin_header', '0', ''); ?><div class="tab-content"><? include template('breadcrumb', '0', ''); ?><?=$_G['message']?>
<fieldset>
<legend>Category list</legend>
<ul><? echo init_categorylist($_G['categorytree']['foodorder']) ?></ul>
</fieldset>
</div><? include template('footer', '0', ''); ?>
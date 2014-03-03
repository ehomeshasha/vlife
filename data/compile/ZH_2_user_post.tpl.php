<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('superadmin_header', '0', ''); ?><section>
<div class="container-fluid">
<div class="row-fluid">
<div class="span12"><? include template('breadcrumb', '0', ''); ?><?=$_G['message']?>
<form class="post_form" method="post" action="" autocomplete="off">
<?=$csrf?>
<input type="hidden" name="submit" value="true" />
<input type="hidden" name="opt" value="<?=$opt?>" />
<input type="hidden" name="uid" value="<?=$user['uid']?>" />
<fieldset>
<legend>Add/Edit User</legend>
<label>Username</label>
    <input type="text" name="username" value="<?=$user['username']?>" placeholder="" >
    <label>Password</label>
    <input type="password" name="password" value="" placeholder="" >
    <label>Level</label>
    <select name="userlevel" id="userlevel">
    <? if(is_array($userlevel_array)) { foreach($userlevel_array as $k => $v) { ?>    	<option value="<?=$v?>"><? echo lang($k); ?></option>
    <? } } ?>    </select>
    <br/>
<button class="btn btn-primary">Submit</button>
</fieldset>
</form>
</div>
</div>
</div>
<section><? include template('footer', '0', ''); ?>
<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('superadmin#header', '0', ''); ?><section>
<div class="container-fluid">
<div class="row-fluid">
<div class="span12"><? include template('breadcrumb', '0', ''); ?><?=$_G['message']?>
<form class="post_form" method="post" action="" autocomplete="off">
<?=$csrf?>
<input type="hidden" name="submit" value="true" />
<input type="hidden" name="opt" value="<?=$opt?>" />
<input type="hidden" name="uid" value="<?=$user['uid']?>" />
<fieldset>
<legend class="mbn"><?=$head_text?></legend>
<div class="control-group">
<label class="control-label" for="inputUsername">Username</label>
<div class="controls">
<input type="text" name="username" value="<?=$user['username']?>" id="inputUsername" placeholder="Username">
</div>
</div>
<div class="control-group">
<label class="control-label" for="inputPassword">Password</label>
<div class="controls">
<input type="password" name="password" id="inputPassword" placeholder="Password">
</div>
</div>
<div class="control-group">
<label class="control-label" for="inputUserLevel">UserLevel</label>
<div class="controls">
<select name="userlevel" id="inputUserLevel">
    <? if(is_array($userlevel_array)) { foreach($userlevel_array as $k => $v) { ?>    	<option value="<?=$v?>" <? if($user['userlevel'] == $v) { ?>selected="selected"<? } ?>><? echo lang($k); ?></option>
    <? } } ?>    </select>
</div>
</div>
<div class="control-group">
<div class="controls">
<button type="submit" class="btn btn-primary" id="login_btn">Submit</button>
</div>
</div>
</fieldset>
</form>
</div>
</div>
</div>
<section><? include template('footer', '0', ''); ?>
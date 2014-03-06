<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('admin#header', '0', ''); ?><div class="tab-content"><? include template('breadcrumb', '0', ''); ?><?=$_G['message']?>
<form action="" method="post" id="category_form" class="post_form">
<?=$csrf?>
<input type="hidden" name="submit" value="true" />
<fieldset>
<legend class="mbn"><?=$head_text?></legend>
<div class="control-group">
<label class="control-label" for="inputName">Name</label>
<div class="controls">
<input type="text" name="name" value="<?=$category['name']?>" id="inputName" placeholder="" maxlength="30" />
</div>
</div>
<div class="control-group">
<label class="control-label" for="inputFid">Father categogry</label>
<div class="controls">
<select name="fid" id="inputFid">
<option value="0">None</option>
<?=$category_html?>
</select>
</div>
</div>
<div class="control-group">
<label class="control-label" for="inputCompanyId">Restaurant</label>
<div class="controls">
<select name="company_id" id="inputCompanyId">
<?=$restaurant_html?>
</select>
</div>
</div>
<div class="control-group">
<label class="control-label" for="inputDisplayorder">Displayorder(0~999)</label>
<div class="controls">
<input type="text" class="input-mini" name="displayorder" value="<?=$displayorder?>" id="inputDisplayorder" placeholder="" maxlength="3" >
</div>
</div>
<div class="control-group">
<div class="controls">
<button type="submit" class="btn btn-primary" id="">Submit</button>
</div>
</div>
</fieldset>
</form>	
</div>
</div></div></div></div>
<script type="text/javascript">
$(function(){
$("#category_form").submit(function(){
if(
chkLength("Category name", $("#inputName").val(), 0, 30) &&
chkDigit("Father category", $("#inputFid").val(), 1, 8) &&
chkDigit("Restaurant", $("#inputCompanyID").val(), 1, 8) &&
chkDigit("Displayorder", $("#inputDisplayorder").val(), 1, 3)
) {
return true;
}
return false;
});



});





</script><? include template('footer', '0', ''); ?>
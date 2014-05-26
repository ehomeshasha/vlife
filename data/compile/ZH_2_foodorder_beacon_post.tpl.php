<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('admin#admin_header', '0', ''); ?><div class="tab-content"><? include template('breadcrumb', '0', ''); ?><?=$_G['message']?>
<form action="" method="post" id="beacon_form" class="post_form">
<?=$csrf?>
<input type="hidden" name="submit" value="true" />
<fieldset>
<legend class="mbn"><?=$head_text?></legend>
<div class="control-group">
<label class="control-label" for="inputName">Name</label>
<div class="controls">
<input type="text" name="name" value="<?=$beacon['name']?>" id="inputName" placeholder="IBeacon name" maxlength="100" />
</div>
</div>
<div class="control-group">
<label class="control-label" for="inputUUID">UUID</label>
<div class="controls">
<input type="text" name="uuid" value="<?=$beacon['uuid']?>" id="inputUUID" placeholder="Input your UUID here" maxlength="255" />
</div>
</div>
<div class="control-group">
<label class="control-label" for="inputRestaurant">Restaurant</label>
<div class="controls">
<select name="company_id" id="inputRestaurant">
<option value="">Select</option><? if(is_array($_G['company_list'])) { foreach($_G['company_list'] as $company) { ?><option value="<?=$company['id']?>" <? if($company['id'] == $beacon['company_id']) { ?>selected="selected"<? } ?>><?=$company['name']?></option><? } } ?></select>
</div>
</div>


<div class="control-group">
<label class="control-label" for="inputDishes">Recommend Dishes</label>
<div class="controls">
<select name="dishes[]" multiple="multiple" id="inputDishes">
<?=$dishes_option?>
</select>
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

$("#inputRestaurant").change(function(){
var inputRestaurant = $(this);
var id = inputRestaurant.val(); 
if(id != "") {

$.ajax({
url: 'index.php?home=misc&act=get_dishes_option',
type: "get",
data: {company_id:id},
success: function(return_data){
$("#inputDishes").html(return_data);
},

});
} else {
$("#inputDishes").html("");
}
});


$("#beacon_form").submit(function(){

if($("#inputDishes").val() == "" ||
$("#inputDishes").val() == null) {
alert("Recommend Dishes can not be empty");
return false;
}

if(
chkLength("IBeacon name", $("#inputName").val(), 0, 100) &&
chkLength("UUID", $("#inputUUID").val(), 0, 255) &&
chkLength("Restaurant", $("#inputRestaurant").val(), 0, 8) 

) {
return true;
}
return false;
});
});
</script><? include template('footer', '0', ''); ?>
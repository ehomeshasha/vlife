<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('header', '0', ''); ?><div class="main">
<?=$_G['message']?>
<div class="row">
<div class="col-lg-12">
<div class="panel panel-info">
<div class="panel-heading">
<h3 class="panel-title">My Orders</h3>
</div>
<div class="panel-body">
<div class="list-group"><? if(is_array($foodArr)) { foreach($foodArr as $row) { ?><div class="list-group-item clearfix food_block">
<div class="pull-left food_image_area mrn">
<a href="#">
<img src="<?=$_G['siteurl']?><?=$row['path']?>" alt="<?=$row['name']?>" class="img-responsive food_image" />
</a>
</div>
<div class="pull-left  food_info_area">
<div class="mbn mtn"><?=$row['name']?></div>
<div class="clearfix">
<table class="table table-condensed no-margin-bottom">
<tr>
<td>
$<? echo number_format($row['price'],2); ?></td>
<td>
<?=$row['food_count']?>
</td>
<td>
$<?=$row['food_totalprice']?>
</td>
</tr>
</table>
</div>
</div>
</div><? } } ?></div>
<div class="order_form">
<form role="form" action="" method="post" id="order_form" class="post_form">
<?=$csrf?>
<input type="hidden" name="submit" value="true" />
<textarea name="food_str" class="hidden"><?=$food_str?></textarea>
<div class="form-group">
<label for="inputPhone">Telephone</label>
   								<input type="text" class="form-control" id="inputPhone" placeholder="" name="phone" value="<?=$_COOKIE['telephone']?>" maxlength="30" />
 							</div>
 							<div class="form-group">
<label for="inputAddress">Order Address</label>
   								<textarea name="address" class="form-control" id="inputAddress" style="height:100px;"><?=$_G['userinfo']['address']?></textarea>
 							</div>
 							<button type="submit" class="btn btn-primary">Submit</button>
</form>
</div>
</div>
</div>


</div>	
</div>
</div></div>
<script type="text/javascript">
$(function(){
$("#order_form").submit(function(){
if(
chkLength("Telephone", $("#inputPhone").val(), 0, 30) &&
chkLength("Your address", $("#inputAddress").val(), 0, 255)
) {

setCookie('telephone', $("#inputPhone").val(), 365);<? if(is_array($foodArr)) { foreach($foodArr as $k => $v) { ?>setCookie('food_count<?=$k?>', '0',-1);<? } } ?>return true;
}
return false;
});
});
</script><? include template('footer', '0', ''); ?>
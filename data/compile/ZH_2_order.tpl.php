<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('header', '0', ''); ?><div class="main">
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
<form role="form" action="" method="post" id="order_post_form">
<?=$csrf?>
<input type="hidden" name="submit" value="true" />
<textarea name="food_str" class="hidden"><?=$food_str?></textarea>
<div class="form-group">
<label for="telephoneInput">Telephone</label>
   								<input type="text" class="form-control" id="telephoneInput" placeholder="" name="telephoneInput" value="<?=$_COOKIE['telephone']?>" />
 								</div>
 								<div class="form-group">
<label for="orderAddressInput">Order Address</label>
   								<textarea name="orderAddressInput" class="form-control" id="orderAddressInput" style="height:100px;"><?=$_COOKIE['order_address']?></textarea>
 								</div>
 								<button type="submit" class="btn btn-primary">Submit</button>
</form>
</div>
</div>
</div>


</div>	
</div>
</div><? include template('footer', '0', ''); ?>
<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('header', '0', ''); ?><div class="main">
<div class="row">
<div class="col-lg-12">
<div class="shop panel panel-success">
<div class="panel-heading">
      	<h3 class="panel-title"><?=$company['name']?></h3>
    </div>
<div class="panel-body">
<div class="shop_image mbm">
<img src="<?=$_G['siteurl']?><?=$patharr['1']?>" alt="" class="img-responsive" />
</div>
<div class="shop_info well well-sm mbm">
<? if(!empty($company['brand'])) { ?>
<p><label>Brand: </label><span class='content'><?=$company['brand']?></span></p>
<? } ?>
<p><label>Restaurant Address: </label><span class='content'><?=$company['address']?></span></p>
<p><label>restaurant phone: </label><span class='content'><?=$company['phone']?></span></p>
<p><label>Main menus: </label><span class='content'><?=$main_menu?>...</span></p>
</div>
<div class="shop_content alert alert-info no-margin-bottom">
<?=$company['description']?>
</div>
</div>
</div>
</div>
</div>
</div></div><? include template('footer', '0', ''); ?>
<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('header', '0', ''); ?><div class="main">
<?=$_G['message']?>
<div class="image_layer" style="position:absolute;top:5em;left:0;z-index:998;padding:8px 8px;background-color:rgba(0,0,0,0.1);display:none;">
<span class="image_close_btn glyphicon glyphicon-remove-sign" style="position:absolute;right:0;top:-5px;font-size:1.5em;z-index:999;cursor:pointer;"></span>
<div class="image_container"></div>	
</div>
<div class="row">
<div class="col-lg-12">
<table class="table table-condensed">
<thead>
<tr>
<th>OrderID</th>
<th>Date</th>
<th>Detail</th>
</tr>
</thead>
<tbody><? if(is_array($orders)) { foreach($orders as $v) { ?><tr>
<td><?=$v['order_id']?></td>
<td><? echo get_abbr_date($v['dateline'], true); ?></td>
<td>
<a href="javascript:;" class="collapse_btn" data-id="<?=$v['id']?>" data-status="collapse">collapse</a><? $dishes = json_decode($v['dishes'], true); ?><div class="hidden">
<ul class="no-margin plw"><? if(is_array($dishes)) { foreach($dishes as $val) { ?><li>
<span class="mrn"><?=$val['name']?></span>
<span class="mrn"><strong><?=$val['food_count']?></strong></span>
<span class="mrn"><strong><?=$val['food_totalprice']?></strong></span>
<a href="javascript:;" class="food_image_link text-danger" data-id="<?=$val['id']?>">
<img src="<?=$_G['siteurl']?><?=$val['path']?>" class="hidden" />
pic
</a>
</li><? } } ?></ul>
</div>
</td>
</tr><? } } ?></tbody>

</table>
</div>	
</div>
</div></div>
<script type="text/javascript">
$(".food_image_link").click(function(){
var src = $(this).find("img").attr("src");
var img = $(this).find("img")[0];
ori_width = parseInt(img.width);
ori_height = parseInt(img.height);
layer_width = parseInt($(window).width());
layer_height = (layer_width/ori_width) * ori_height;
img_width = layer_width - 16;
img_height = layer_height - 16;



$(".image_layer").css("width", layer_width).css("height", layer_height);
$(".image_container").html("<img src='"+src+"' width='"+img_width+"' height='"+img_height+"' '>")
$(".image_layer").fadeIn();



});

$(".image_close_btn").click(function(){
$(".image_layer").fadeOut();
});
</script><? include template('footer', '0', ''); ?>
<? if(!defined('IN_SYSTEM')) exit('Access Denied'); ?>
<form class="z form-inline" action="" method="post" id="form-perpage">
<span class="help-inline">每页显示</span>
<select name="perpage" class="input-small" id="perpage">
<option value="5">5</option>
<option value="10">10</option>
<option value="20" selected="selected">20</option>
<option value="50">50</option>
<option value="100">100</option>
<option value="500">500</option>
<option value="1000">1000</option>
</select>
</form>
<script type="text/javascript">
$(function(){
$("#perpage").val("<?=$perpage?>");
$("#perpage").change(function(){
var action = location.href.replace(/&page=[\d]+/g, "");
$("#form-perpage").attr("action",action);
$("#form-perpage").submit();
});
});
</script>
<? if(!defined('IN_SYSTEM')) exit('Access Denied'); include template('admin#admin_header', '0', ''); ?><div class="tab-content"><? include template('breadcrumb', '0', ''); ?><?=$_G['message']?>
<form action="" method="post" id="dishes_form" class="post_form">
<?=$csrf?>
<input type="hidden" name="submit" value="true" />
<fieldset>
<legend class="mbn"><?=$head_text?></legend>
<div class="control-group">
<label class="control-label" for="inputName">Name</label>
<div class="controls">
<input type="text" name="name" value="<?=$dish['name']?>" id="inputName" placeholder="" maxlength="30" />
</div>
</div>
<div class="control-group">
<label class="control-label" for="inputName">Price</label>
<div class="controls">
<input type="text" name="price" value="<?=$dish['price']?>" id="inputPrice" placeholder="" maxlength="30" />
</div>
</div>
<div class="control-group">
<label class="control-label" for="inputCategory">Categogry</label>
<div class="controls">
<select name="cid" id="inputCategory">
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
<label class="control-label" for="inputDescription">Short Description</label>
<div class="controls">
<textarea name="description" id="inputDescription" placeholder="Short Description" rows="5"><?=$dish['description']?></textarea>
</div>
</div>
<div class="control-group">
<label class="control-label" for="file_upload">Thumb</label>
<div class="controls">
<ul id="picture_show" class="" style="margin-left:0;list-style:none;">
<? if(!empty($filepatharr['0'])) { if(is_array($filepatharr)) { foreach($filepatharr as $v) { ?>       <? $filearr = explode("^", $v); ?>       	<li>
       		<div>
       		<a href="<?=$_G['siteurl']?><?=$filearr['1']?>" target="_blank"><?=$filearr['0']?></a>
       		<a href='javascript:;' class='cancel_upload'>
       			<img src='<?=$_G['siteurl']?>uploadify/uploadify-cancel.png' width='12' height='12'>
       		</a>
       		<input class='filepath' type='hidden' name='filepath[]' value='<?=$v?>'/>
       		</div>
       		<div>
       			<img src="<?=$_G['siteurl']?><?=$filearr['1']?>" style="width:260px;height:195px" />
       		</div>
       		
       	</li>
       	<? } } ?>       	<? } ?>
       	</ul>
       	<input id="file_upload" name="file_upload" type="file">
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
$('#file_upload').uploadify({
'multi'    : false,
'buttonText' : 'Upload',
'width' : '60',
'height'   : '20',
'removeTimeout' : 999,
'fileSizeLimit' : '2MB',
'formData'     : {
'timestamp' : "<?=$_G['timestamp']?>",
'token'     : "<? echo md5('unique_salt'.$_G['timestamp']); ?>"
},
'checkExisting' : 'uploadify/check-exists.php',
'swf'      : '<?=$_G['siteurl']?>uploadify/uploadify.swf',
'uploader' : '<?=$_G['siteurl']?>uploadify/uploadify.php?username=uid_<?=$_G['uid']?>',

'onUploadStart' : function(file) {
$("#file_upload input").remove();
if($("#file_upload-queue .uploadify-queue-item").length > 1) { 
$("#file_upload-queue .uploadify-queue-item:first").remove();
}
var status = 0;
var ext = new Array('jpg','jpeg','gif','png','bmp');

for (i=0;i<ext.length;i++) {
if(ext[i] == file.type.substr(1).toLowerCase()) {
status = 1; 
}
}
if(status == 0) {
alert("Invalid filetype, Pictures only for uploading");
javascript:$('#file_upload').uploadify('cancel',file.id);	
}
        },
'onUploadSuccess' : function(file, data, response) {//每次成功上传后执行的回调函数，从服务端返回数据到前端
if(data == "invalid filetype") {
alert("Invalid filetype, Pictures only for uploading");
$("#" + file.id).css("display","none");
} else {
$("#file_upload").append('<input class="filepath" id="input_'+ file.id +'" type="hidden" name="filepath[]" value="'+ file.name + '^' + data +'"/>');
$("#picture_show").html("<li><img src='<?=$_G['siteurl']?>"+data+"' style='width:260px;height:195px;' /></li>");
}
},
/*
'onCancel' : function(file) {
            alert('The file ' + file.name + ' was cancelled.');
        },
        */
        'onUploadError' : function(file, errorCode, errorMsg, errorString) {
            alert('uploading '+file.name + ' failed: ' + errorString);
        }
});
<? if(!empty($filepatharr['0'])) { ?>
$(".cancel_upload").click(function(){
if(confirm("cancel this upload?")) {
$(this).parent().parent().remove();
var path = $(this).next().val();
$.post('<?=$_G['siteurl']?>index.php?home=misc&act=cancel_upload',{table_name: 'dishes', id:'<?=$dish['id']?>',path:path});
}
})
<? } ?>



$("#dishes_form").submit(function(){
if(
chkLength("Dish name", $("#inputName").val(), 0, 30) &&
chkDigit("Restaurant", $("#inputCompanyID").val(), 1, 8) &&
chkLength("Short description", $("#inputDescription").val(), -1, 255) &&
chkNumber("Price", $("#inputPrice").val()) &&
chkDigit("Category", $("#inputCategory").val(), 1, 8) &&
chkDigit("Displayorder", $("#inputDisplayorder").val(), 1, 3) &&
chkUploadExist("Dish thumb", $(".filepath"))
) {
return true;
}
return false;
});



});





</script><? include template('footer', '0', ''); ?>
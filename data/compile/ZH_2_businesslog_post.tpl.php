<? if(!defined('IN_JZ100')) exit('Access Denied'); ?>
<div class="row-fluid">
<div class="span12">
<p class="post_info">
<? if($_G['action'] == 'post') { ?>
<span>发布人：<?=$_G['username']?></span><span>发布日期：<? echo date("Y-m-d", $_G['timestamp']); ?></span>
<? } elseif($_G['action'] == 'edit') { ?>
<span>发布人：<?=$businesslog['username']?></span><span>发布日期：<? echo get_abbr_date($businesslog['dateline'], true); ?></span>
<? } ?>
</p>
</div>		
</div>
<? if($_G['action'] == 'post') { ?>
<form action="index.php?home=businesslog&amp;act=post" method="post" id="form" onsubmit="$(this).find('input:checkbox').removeAttr('disabled');" enctype="multipart/form-data">
<input type="hidden" name="submit" value="true" />
<? } elseif($_G['action'] == 'edit') { ?>
<form action="index.php?home=businesslog&amp;act=edit&amp;bid=<?=$bid?>" method="post" id="form" onsubmit="$(this).find('input:checkbox').removeAttr('disabled')" enctype="multipart/form-data">
<input type="hidden" name="submit" value="true" />
<? } ?>
<div class="row-fluid">
<div class="span12">
<ul class="nav nav-tabs">
<li class="active"><a href="#">今日工作</a></li>
        </ul>
<div class="row-fluid">
<div class="span8">
<textarea rows="5"  maxlen="5000" class="input_check big_textarea span12" name="todayplan" id="todayplan"><?=$businesslog['todayplan']?></textarea>
</div>
<div class="span4">
<div class="alert" class="messagetip" rows="3">
  	已输入<strong class="inputnum">0</strong>字,<span class="wordview">还可以输入</span><strong class="leftnum">5000</strong>字
</div>
</div>
</div>
<ul class="nav nav-tabs">
<li class="active"><a href="#">完成情况</a></li>
        </ul>
<div class="row-fluid">
<div class="span8">
<textarea rows="5" maxlen="5000" class="input_check big_textarea span12" name="fulfil" id="todayplan"><?=$businesslog['fulfil']?></textarea>
</div>
<div class="span4">
<div class="alert" class="messagetip">
  	已输入<strong class="inputnum">0</strong>字,<span class="wordview">还可以输入</span><strong class="leftnum">5000</strong>字
</div>
</div>
</div>
<ul class="nav nav-tabs">
<li class="active"><a href="#">经验总结</a></li>
        </ul>
<div class="row-fluid">
<div class="span8">
<textarea rows="5" maxlen="5000" class="input_check big_textarea span12" name="summary" id="todayplan"><?=$businesslog['summary']?></textarea>
</div>
<div class="span4">
<div class="alert" class="messagetip">
  	已输入<strong class="inputnum">0</strong>字,<span class="wordview">还可以输入</span><strong class="leftnum">5000</strong>字
</div>
</div>
</div>
<ul class="nav nav-tabs">
<li class="active"><a href="#">明天计划</a></li>
        </ul>
<div class="row-fluid">
<div class="span8">
<textarea rows="5" maxlen="5000" class="input_check big_textarea span12" name="tomorrowplan" id="todayplan"><?=$businesslog['tomorrowplan']?></textarea>
</div>
<div class="span4">
<div class="alert" class="messagetip">
  	已输入<strong class="inputnum">0</strong>字,<span class="wordview">还可以输入</span><strong class="leftnum">5000</strong>字
</div>
</div>
</div>
<ul class="nav nav-tabs">
<li class="active"><a href="#">@人员列表</a></li>
        </ul>
<div class="row-fluid">
<div class="span8">
<div class="mbw memberlist">
<?=$userlist_html?>
</div>
</div>
<div class="span4">
<div class="alert" class="messagetip">
  	请选择要@的人,该工作日志会出现在对方的@工作日志列表里
</div>
</div>
</div>
<ul class="nav nav-tabs">
<li class="active"><a href="#">补充文档(支持图片,word,excel,和pdf)</a></li>
        </ul>
        
       	<? if($_G['action'] == 'edit' && !empty($filepatharr['0'])) { ?>
       	<label>已上传的文件：</label>
       	<ul class="well" style="margin:15px 0;list-style:none;">
       <? if(is_array($filepatharr)) { foreach($filepatharr as $v) { ?>       <? $filearr = explode("^", $v); ?>       	<li>
       		<a href="<?=$filearr['1']?>" target="_blank"><?=$filearr['0']?></a>
       		<a href='javascript:;' class='cancel_upload'>
       			<img src='uploadify/uploadify-cancel.png' width='16' height='16'>
       		</a>
       		<input class='filepath' type='hidden' name='filepath[]' value='<?=$v?>'/>
       	</li>
       	<? } } ?>       	</ul>
       	<? } ?>
        
        <div id="upload">
<input id="file_upload" name="file_upload" type="file" multiple="true">
</div>
</div>
</div>
<div class="divide-line"></div>
<div>
<button class="btn btn-primary " type="submit">提交</button>
</div>
</form>
<script type="text/javascript">
$(function(){
$('#file_upload').uploadify({
'buttonText' : '上传文件',
'height'   : '26',
'removeTimeout' : 999,
'fileSizeLimit' : '10MB',
'formData'     : {
'timestamp' : "<?=$_G['timestamp']?>",
'token'     : "<? echo md5('unique_salt'.$_G['timestamp']); ?>"
},
'checkExisting' : 'uploadify/check-exists.php',
'swf'      : 'uploadify/uploadify.swf',
'uploader' : 'uploadify/uploadify.php?username=uid_<?=$_G['uid']?>',

'onUploadStart' : function(file) {

var status = 0;
var ext = new Array('jpg','jpeg','gif','png','pdf','doc','docx','xls','csv');
for (i=0;i<ext.length;i++) {
if(ext[i] == file.type.substr(1).toLowerCase()) {
status = 1; 
}
}
if(status == 0) {
alert(file.name + "的文件类型不符合,只能上传图片,doc,excel或excel类型");
javascript:$('#file_upload').uploadify('cancel',file.id);	
}
        },
'onUploadSuccess' : function(file, data, response) {//每次成功上传后执行的回调函数，从服务端返回数据到前端
if(data == "文件类型不符合") {
/*
for (var i in file){
//alert(i);            // 输出属性名  
//alert(file[i]);        // 输出属性的值
alert(i + ":" + file[i]);
}
*/
alert(file.name + "的文件类型不符合");
$("#" + file.id).css("display","none");
} else {
$("#file_upload").append('<input class="filepath" id="input_'+ file.id +'" type="hidden" name="filepath[]" value="'+ file.name + '^' + data +'"/>')
}
},
/*
'onCancel' : function(file) {
            alert('The file ' + file.name + ' was cancelled.');
        },
        */
        'onUploadError' : function(file, errorCode, errorMsg, errorString) {
            alert('文件' + file.name + '上传失败: ' + errorString);
        }
});
<? if($_G['action'] == 'edit') { ?>
$(".cancel_upload").click(function(){
if(confirm("取消这个文件的上传")) {
$(this).parent().remove();
var path = $(this).next().val();
$.post('index.php?home=misc&act=cancel_upload',{bid:'<?=$businesslog['bid']?>',path:path});
}
})
<? } ?>
});
</script>
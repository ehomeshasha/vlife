<? if(!defined('IN_JZ100')) exit('Access Denied'); include template('header', '0', ''); ?><section>
<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
<ul class="breadcrumb">
  	<li><a href="index.php">首页</a> <span class="divider">/</span></li>
  	<li><a href="#">OA管理</a> <span class="divider">/</span></li>
  	<li><a href="<?=$_G['cur_link']?>">管理用户</a></li>
</ul>

<div>
<form class="form-inline" action="index.php?home=admusers" method="post" id="search_form">
  	<? if($_G['userlevel'] == 9 || $_G['userlevel'] == 2) { ?>
<span class="help-inline">用户名：</span>
  	<input type="text" name="username" value="<?=$username?>" maxlength="32" />
  	<? } ?>
  	&nbsp;&nbsp;
  	<button type="submit" class="btn btn-primary search_btn">查找</button>
</form>
</div>


<table class="table table-hover table-bordered">
<tr>
    	<th width="3%">UID</th>
    	<th width="8%">用户名</th>
    	<th width="6%">用户级别</th>
<th width="6%">职务</th>
<th width="6%">PM用戶組</th>
    	<th width="9%">注册日期</th>
<th width="9%">密码</th>
    </tr><? if(is_array($datalist)) { foreach($datalist as $k => $v) { ?><tr>
<td class="uid"><?=$v['uid']?></td>
    	<td><?=$v['username']?></td>
    	<td>
<select class="adm_ul input-small <? if(!empty($v['team_score'])) { ?>bg-green<? } ?>" data-toggle="tooltip" data-trigger="manual" data-title="" data-placement="bottom" style="margin-bottom:0px;">
    <? if(is_array($_G['ArrayData']['admuserlevel'])) { foreach($_G['ArrayData']['admuserlevel'] as $kk => $vv) { ?>    <? $selected = !empty($v['userlevel']) && $v['userlevel'] == $kk ?  "selected='selected'" : "";
    		 ?>    		<option value='<?=$kk?>' <?=$selected?>><?=$vv?></option>
    <? } } ?>    		</select>
</td>
<td>
<select class="adm_dt input-small <? if(!empty($v['team_score'])) { ?>bg-green<? } ?>" data-toggle="tooltip" data-trigger="manual" data-title="" data-placement="bottom" style="margin-bottom:0px;">
    <? if(is_array($_G['ArrayData']['dutieslevel'])) { foreach($_G['ArrayData']['dutieslevel'] as $kk => $vv) { ?>    <? $selected = !empty($v['manager']) && $v['manager'] == $kk ?  "selected='selected'" : "";
    		 ?>    		<option value='<?=$kk?>' <?=$selected?>><?=$vv?></option>
    <? } } ?>    		</select>
</td>
<td><select class="adm_pm input-small <? if($v['PM']) { ?>bg-green<? } ?>" data-toggle="tooltip" data-trigger="manual" data-title="" data-placement="bottom" style="margin-bottom:0px;"><option value='0'>否</option><? if($v['PM']) { ?><option value='1' selected='selected'>是</option><? } else { ?><option value='1'>是</option><? } ?></select></td>
<td>&nbsp; <?=$v['dateline']?></td>
<td>&nbsp; <a data-toggle="modal" data-href="index.php?home=login&amp;act=editpasswords&amp;uids=<?=$v['uid']?>" href="">修改密码</a></td>
</tr><? } } ?></table>




<form action="index.php?home=login&amp;act=register" method="post" id="form" style="display:none;">
<p>用户名：</p>
<input type="text" name="username" maxlength="32" value="" />
<p>密码：</p>
<input type="password" name="password" maxlength="32" value="" />
<p>用户级别:</p>
<select name="userlevel">
<option value="1">网编</option>
<option value="0">技术</option>
<option value="2">总编</option>
<option value="9">管理员</option>
<option value="11">实习生</option>
</select>
<div>
<button class="btn btn-primary" type="submit">提交</button>
</div>
</form>
</div>
</div>
</div>
</section><? include template('footer', '0', ''); ?>
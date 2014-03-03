<? if(!defined('IN_JZ100')) exit('Access Denied'); include template('header', '0', ''); ?><section>
<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
<ul class="breadcrumb">
  	<li><a href="index.php">首页</a> <span class="divider">/</span></li>
  	<li><a href="<?=$_G['cur_link']?>">OA成员(帖子)考评统计</a></li>
</ul>
<?=$_G['message']?>
<div>
<form class="form-inline" action="index.php?home=member" method="post" id="search_form">
<span class="help-inline">统计开始时间：</span>
<input class="datepicker datetext" type="text" value="<?=$date['startdate']?>" name="startdate" id="startdate">
<span class="help-inline">统计结束时间：</span>
  	<input class="datepicker datetext" type="text" value="<?=$date['enddate']?>" name="enddate" id="enddate">
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

    	<td width="5%">UID</td>
    	<td>用户名</td>
    	
    	<td>帖子数</td>
    	<td>帖子总分</td>
    	<td>帖子平均分</td>
    	<td>注册日期</td>
    </tr><? if(is_array($datalist)) { foreach($datalist as $k => $v) { ?><tr>
<td><?=$v['uid']?></td>
    	<td><?=$v['username']?></td>
    	
    	<td><?=$v['threadcount']?></td>
    	<td><?=$v['threadtotalscore']?></td>
    	<td><? echo format_score($v['threadaveragescore']); ?></td>
    	<td><? echo date("Y-m-d", $v['dateline']); ?></td>
    </tr><? } } ?></table>
<div class="pagination pagination-right"><? include template('perpage', '0', ''); ?><?=$multi?>
</div>
</div>
</div>
</div>
<section><? include template('footer', '0', ''); ?>
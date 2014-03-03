<? if(!defined('IN_JZ100')) exit('Access Denied'); include template('header', '0', ''); ?><section>
<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
<ul class="breadcrumb">
  	<li><a href="index.php">首页</a> <span class="divider">/</span></li>
  	<li><a href="<?=$_G['cur_link']?>">OA成员(工作)考评统计</a></li>
</ul>
<?=$_G['message']?>
<div>
<form class="form-inline" action="index.php?home=memberlog" method="post" id="search_form">
<!--<span class="help-inline">统计开始时间：</span>
<input class="datepicker datetext" style="width:90px;" type="text" value="<?=$date['startdate']?>" name="startdate" id="startdate">-->
<span class="help-inline">选择时间：</span>
  	<input class="datepicker datetext" style="width:90px;" type="text" onmouseover="ch_date()" value="<?=$date['enddate']?>" name="enddate" id="enddate">
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
    	<th width="6%">日志数</th>
    	<th width="9%">日志分数</th>
    	<th width="9%">团队分数</th>
    	<th width="9%">创新分数</th>
<th width="9%">所得总分</th>
<th>考评详细</th>
    	<th width="9%">考评日期</th>
    </tr><? if(is_array($datalist)) { foreach($datalist as $k => $v) { ?><tr>
<td class="uid"><?=$v['uid']?></td>
    	<td><?=$v['username']?></td>
    	<td><?=$v['logcount']?></td>
    	<td>
<? if($_G['userlevel'] == 2 || $_G['userlevel'] == 9) { ?>
    		<select class="score_log input-small <? if(!empty($v['log_score'])) { ?>bg-green<? } ?>" data-toggle="tooltip" data-trigger="manual" data-title="" data-placement="bottom">
    		<? if(empty($v['log_score'])) { ?>
    		<option value="" class='score_default'>评分</option>
    		<? } ?>
    <? if(is_array($_G['ArrayData']['scorelevel_log'])) { foreach($_G['ArrayData']['scorelevel_log'] as $kk => $vv) { ?>    <? $score_value = $_G['ArrayData']['scoretype'][$v['manager']]['score'][$kk];
    		$selected = !empty($v['log_score']) && $v['log_score'] == $score_value ?  "selected='selected'" : "";
    		 ?>    		<option value='<?=$score_value?>' <?=$selected?>><?=$vv?>(<?=$score_value?>分)</option>
    <? } } ?>    		<? if(!empty($v['log_score'])) { ?>
    		<option value="-1">取消评分</option>
    		<? } ?>
    		</select>
    		<? } else { ?>

    <? echo  $v['log_score']  ?>    		<? } ?>
</td>
    	<td>

<? if($_G['userlevel'] == 2 || $_G['userlevel'] == 9) { ?>
    		<select class="score_team input-small <? if(!empty($v['team_score'])) { ?>bg-green<? } ?>" data-toggle="tooltip" data-trigger="manual" data-title="" data-placement="bottom">
    		<? if(empty($v['team_score'])) { ?>
    		<option value="" class='score_default'>评分</option>
    		<? } ?>
    <? if(is_array($_G['ArrayData']['scorelevel_log'])) { foreach($_G['ArrayData']['scorelevel_log'] as $kk => $vv) { ?>    <? $score_value = $_G['ArrayData']['scoretype'][$v['manager']]['score'][$kk];
    		$selected = !empty($v['team_score']) && $v['team_score'] == $score_value ?  "selected='selected'" : "";
    		 ?>    		<option value='<?=$score_value?>' <?=$selected?>><?=$vv?>(<?=$score_value?>分)</option>
    <? } } ?>    		<? if(!empty($v['team_score'])) { ?>
    		<option value="-1">取消评分</option>
    		<? } ?>
    		</select>
    		<? } else { ?>
    <? echo  $v['team_score'] ?>    		<? } ?>
</td>
    	<td>
<? if($_G['userlevel'] == 2 || $_G['userlevel'] == 9) { ?>
    		<select class="score_innovate input-small <? if(!empty($v['innovate'])) { ?>bg-green<? } ?>" data-toggle="tooltip" data-trigger="manual" data-title="" data-placement="bottom">
    		<? if(empty($v['innovate'])) { ?>
    		<option value="" class='score_default'>评分</option>
    		<? } ?>
    <? if(is_array($_G['ArrayData']['scorelevel_log'])) { foreach($_G['ArrayData']['scorelevel_log'] as $kk => $vv) { ?>    <? $score_value = $_G['ArrayData']['scoretype'][$v['manager']]['score'][$kk];
    		$selected = !empty($v['innovate']) && $v['innovate'] == $score_value ?  "selected='selected'" : "";
    		 ?>    		<option value='<?=$score_value?>' <?=$selected?>><?=$vv?>(<?=$score_value?>分)</option>
    <? } } ?>    		<? if(!empty($v['innovate'])) { ?>
    		<option value="-1">取消评分</option>
    		<? } ?>
    		</select>
    		<? } else { ?>
    <? echo  $v['innovate'] ?>    		<? } ?>
</td>
<td>&nbsp; <? if(!empty($v['score_count'])) { ?><?=$v['score_count']?> 分 <? } ?></td>
<td>
<? if($_G['userlevel'] == 2 || $_G['userlevel'] == 9) { ?>
    			<textarea class="score_txt" style="width:600px; margin-bottom:0px;" cols="100" rows="2"><?=$v['txt']?></textarea>
    		<? } else { ?>
&nbsp; <?=$v['txt']?></td>
<? } ?>
    	<td>&nbsp; <?=$v['dateline']?></td>
    </tr><? } } ?></table>
<div class="pagination pagination-right"><? include template('perpage', '0', ''); ?><?=$multi?>
</div>
</div>
</div>
</div>
<section><? include template('footer', '0', ''); ?><script>

function ch_date(){
document.getElementById("date_days").style.display="none";
document.getElementById("date_months").style.display="block";
}


</script>
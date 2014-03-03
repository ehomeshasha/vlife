<? if(!defined('IN_SYSTEM')) exit('Access Denied'); ?>
<!DOCTYPE html>
<html>
<head>
<title>家长100 OA系统</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="views/default/css/bootstrap.min.css" type="text/css" rel="stylesheet" media="screen">
<link href="views/default/css/bootstrap-responsive.min.css" type="text/css" rel="stylesheet" media="screen">
<link href="views/default/css/datepicker.css" type="text/css" rel="stylesheet">
<!--<link href="views/default/css/datepicker.less" type="text/css" rel="stylesheet/less">-->
<link href="views/default/css/common.css" type="text/css" rel="stylesheet">
<script src="views/default/js/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="views/default/js/bootstrap.min.js" type="text/javascript"></script>
<script src="views/default/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="views/default/js/common.js" type="text/javascript"></script>
<script src="uploadify/jquery.uploadify.min.js" type="text/javascript" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="uploadify/uploadify.css">
<script type="text/javascript">
var controller = "<?=$_G['controller']?>";
var action = "<?=$_G['action']?>";
<? if(!empty($_G['message'])) { ?>
setTimeout(function() {
$("#message").fadeOut(1000);
}, 3000);
setTimeout(function() {
$(".clear_session").trigger('click');
}, 4000);
<? } ?>
</script>
</head>
<body>
<header>
<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
<div class="navbar">
<div class="navbar-inner"><a class="brand" href="index.php">家长100 OA系统</a>
<? if($_G['uid']) { ?>
<ul class="nav jznav">
<li <? if($_G['active_nav'] == 'index') { ?>class="active"<? } ?>>
    				<a href="index.php">首页</a>
  				</li>
  				<li class="dropdown <? if($_G['active_nav'] == 'businesslog') { ?>active<? } ?>">
<a href="index.php?home=businesslog">工作日志</a>
</li>
<li class="dropdown <? if($_G['active_nav'] == 'thread') { ?>active<? } ?>">
<a href="index.php?home=thread">论坛帖子</a>
</li>
<li class="dropdown <? if($_G['active_nav'] == 'membercenter') { ?>active<? } ?>">
<a class="dropdown-toggle" data-toggle="dropdown" href="#">个人中心<b class="caret"></b></a>
<ul class="dropdown-menu">
<li><a href="index.php?home=businesslog&amp;act=post">发布工作日志</a></li>
<li><a href="index.php?home=businesslog&amp;view=me">我的工作日志</a></li>
<li><a href="index.php?home=businesslog&amp;mention=me">@我的日志</a></li>
<li class="divider"></li>
<li><a href="index.php?home=thread&amp;act=post">发布论坛帖子</a></li>
<li><a href="index.php?home=thread&amp;view=me">我的帖子</a></li>
<li><a href="index.php?home=thread&amp;mention=me">@我的帖子</a></li>
<li class="divider"></li>
<? if($_G['userlevel'] != 9) { ?>
<li><a href="index.php?home=member">我的(帖子)考评统计</a></li>
<li><a href="index.php?home=memberlog">我的(工作)考评统计</a></li>
<? } ?>
<li><a href="index.php?home=draft">草稿箱</a></li>
<li><a data-toggle="modal" data-href="index.php?home=login&amp;act=editpassword" href="">修改密码</a></li>
</ul>
</li>
<? if($_G['userlevel'] == 9) { ?>
<li class="dropdown <? if($_G['active_nav'] == 'admincenter') { ?>active<? } ?>">
<a class="dropdown-toggle" data-toggle="dropdown" href="#">OA管理<b class="caret"></b></a>
<ul class="dropdown-menu">
<li><a href="index.php?home=login&amp;act=adduser">添加新用户</a></li>
<li><a href="index.php?home=admusers">管理用户</a></li>
<li><a href="index.php?home=member">OA成员(帖子)考评统计</a></li>
<li><a href="index.php?home=memberlog">OA成员(工作)考评统计</a></li>
<li><a data-toggle="modal" data-href="index.php?home=bulletin&amp;act=post" href="">发布OA公告</a></li>
<li><a href="index.php?home=bulletin">OA公告列表</a></li>
<li><a href="index.php?home=recyclebin">回收站</a></li>
</ul>
</li>
<? } ?>
</ul>
<ul class="nav pull-right">
<li><a href="#" style="color:#000;"><i class="<? echo geticon($_G['userlevel']); ?> mrn"></i><?=$_G['username']?></a></li>
<li><a href="index.php?home=login&amp;act=logout">退出</a></li>
</ul>
<? } ?>
</div>
</div>
</div>
</div>
</div>
</header>
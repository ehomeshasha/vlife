<? if(!defined('IN_SYSTEM')) exit('Access Denied'); ?>
<!DOCTYPE html>
<html>
<head>
<title>life system</title>
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
<div class="navbar-inner"><a class="brand" href="index.php?home=superadmin_user">SuperAdmin Center</a>
<ul class="nav jznav">
<li class="<? if($_G['active_nav'] == 'superadmin_user') { ?>active<? } ?>"><a href="index.php?home=superadmin_user">Users</a></li>
</ul>
<ul class="nav pull-right">
<li><a href="#" style="color:#000;"><i class="<? echo geticon($_G['userlevel']); ?> mrn"></i><?=$_G['username']?></a></li>
<li><a href="index.php">HomePage</a></li>
<li><a href="index.php?home=login&amp;act=logout">Logout</a></li>
</ul>
</div>
</div>
</div>
</div>
</div>
</header>
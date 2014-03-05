<? if(!defined('IN_SYSTEM')) exit('Access Denied'); ?>
<!DOCTYPE html>
<html>
<head>
<title>Vlife | Sign In</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="<?=SITE_ROOT?>views/default/css/bootstrap.min.css" type="text/css" rel="stylesheet" media="screen">
<link href="<?=SITE_ROOT?>views/default/css/bootstrap-responsive.min.css" type="text/css" rel="stylesheet" media="screen">
<link href="<?=SITE_ROOT?>views/default/css/datepicker.css" type="text/css" rel="stylesheet">
<!--<link href="views/default/css/datepicker.less" type="text/css" rel="stylesheet/less">-->
<link href="<?=SITE_ROOT?>views/default/css/common.css" type="text/css" rel="stylesheet">
<script src="<?=SITE_ROOT?>views/default/js/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="<?=SITE_ROOT?>views/default/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?=SITE_ROOT?>views/default/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="<?=SITE_ROOT?>views/default/js/common.js" type="text/javascript"></script>
<script src="<?=SITE_ROOT?>uploadify/jquery.uploadify.min.js" type="text/javascript" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="<?=SITE_ROOT?>uploadify/uploadify.css">
<script type="text/javascript">
var controller = "<?=$_G['controller']?>";
var action = "<?=$_G['action']?>";
var site_url = "<?=$_G['siteurl']?>";
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
<div class="navbar-inner">
<a class="brand" href="<?=$_G['siteurl']?>">Vlife</a>
<? if($_G['uid']) { ?>
<ul class="nav pull-right">
<li><a href="#" style="color:#000;"><i class="<? echo geticon($_G['userlevel']); ?> mrn"></i><?=$_G['username']?></a></li>
<li><a href="<?=SITE_ROOT?>index.php?home=login&act=logout">Signout</a></li>
</ul>
<? } ?>
</div>
</div>
</div>
</div>
</div>
</header>
<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
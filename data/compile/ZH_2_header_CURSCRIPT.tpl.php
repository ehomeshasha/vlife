<? if(!defined('IN_SYSTEM')) exit('Access Denied'); ?>
<!DOCTYPE html>
<html lang="en-us">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- <meta name="viewport" content="width=device-width, initial-scale=1.0" /> -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="keywords" content=" " />
<meta name="description" content=" " />
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" type="text/css"/>
<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css" type="text/css"/>
<script src="views/default/js/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="views/default/js/front.js" type="text/javascript"></script>
<link rel="stylesheet" href="views/default/css/common.css" type="text/css"/>
<link rel="icon" href="" type="image/x-icon" />
<link rel="shortcut icon" href="" type="image/x-icon" />
<title> Enjoy the quality of life, to try Home Dishes! </title>
</head>
<body>
<div class="container body" id="body">
<div class="header">
<nav class="navbar navbar-default" id="navigation" role="navigation" style="">
<!-- Brand and toggle get grouped for better mobile display -->
<div class="navbar-header">
<ul class="nav navbar-nav" style="margin:0;">
<li class="pull-left <? if($_G['active_nav']['model'] == 'index') { ?>active<? } ?>"><a href=".">HOME</a></li>
<li class="pull-left <? if($_G['active_nav']['model'] == 'menu') { ?>active<? } ?>"><a href="index.php?home=menu">Menu</a></li>
<li class="pull-left <? if($_G['active_nav']['model'] == 'orders') { ?>active<? } ?>"><a href="index.php?home=order">Orders</a></li>
<li class="pull-left <? if($_G['active_nav']['model'] == 'setting') { ?>active<? } ?>"><a href="index.php?home=setting">Setting</a></li>
</ul>
</div>
</nav>
</div>
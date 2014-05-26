<?php
session_start();
global $_G;
$_G['system_model'] = 0;
require_once('inc/common.inc.php');
require_once(ROOT_PATH.'/inc/cookies.class.php');
require_once(ROOT_PATH.'/inc/session.class.php');
$cookies = new cookies();
$session = new session();

//controler
$home = addslashes(trim($_POST['home'])) ? addslashes(trim($_POST['home'])) : addslashes(trim($_GET['home']));
$act  = addslashes(trim($_POST['act'])) ? addslashes(trim($_POST['act'])) : addslashes(trim($_GET['act']));


/*
if($_GET['uids']){
	$uids = addslashes(trim($_GET['uids']));
	$_G['uids'] = $uids;
	
}*/

$controller = empty($home) ? 'index' : $home;
$action		= empty($act) ? 'index' : $act;
if(!is_file(ROOT_PATH.'/controls/'.$controller.'.class.php')) {
	$controller='index';
	$action='index';
}
if($_POST['submit'] != 'true' && !empty($_COOKIE['telephone'])) {
	$_POST['username'] = $_POST['password'] = trim($_COOKIE['telephone']);
	include_once ROOT_PATH.'./controls/login.class.php';
	$login = new login_controller();
	$login->login_action(1);
}

check_login();
$_G['controller'] = $controller;
$_G['action'] = $action;
$_G['active_nav'] = get_active_nav();
$_G['company_id'] = getgpc('company_id');
if(!empty($_G['company_id'])) {
	$_SESSION['company_id'] = $_G['company_id'];
} else {
	$_G['company_id'] = empty($_SESSION['company_id'])? $_G['setting']['company_id'] : $_SESSION['company_id'];
}
$_G['weixin'] = getgpc('weixin');
if(!empty($_G['weixin'])) {
	$company = $GLOBALS['db']->fetch_first("SELECT id FROM ".tname('company')." WHERE weixin='{$_G['weixin']}'");
	$_G['company_id'] = $company['id'];
}

//echo " home=".$_G['controller']." act=".$_G['action'];
if(!empty($_GET['mobile_app'])) {
	$_G['mobile_app'] = $_SESSION['mobile_app'] = $_GET['mobile_app'];
} else {
	$_G['mobile_app'] = empty($_SESSION['mobile_app'])? "" : $_SESSION['mobile_app'];
}

$_G['cur_link'] = 'index.php?home='.$_G['controller'].'&act='.$_G['action'];
$_G['message'] = initmessage('front');
require_once ROOT_PATH.'/controls/'.$controller.'.class.php';

$conclass = $controller.'_controller';
$actfunc  = $action.'_action';
$views    = new $conclass();
$views->$actfunc();

/*
if($_GET['uids']){
echo " ac=".$actfunc;
echo " uids2=".$_G['uids'];
return;
}*/

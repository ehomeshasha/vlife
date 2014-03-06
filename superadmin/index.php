<?php
session_start();
global $_G;
$_G['system_model'] = 2;
define(SUPERADMIN_DIR, 'superadmin');
require_once('../inc/common.inc.php');
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

$controller = empty($home) ? 'user' : $home;
$action		= empty($act) ? 'index' : $act;
if(!is_file(ROOT_PATH.'/'.SUPERADMIN_DIR.'/controls/'.$controller.'.class.php')) {
	$controller='index';
	$action='index';
}
check_login('superadmin');

$_G['controller'] = $controller;
$_G['action'] = $action;
$_G['active_nav'] = get_active_nav();

//echo " home=".$_G['controller']." act=".$_G['action'];

$_G['cur_link'] = 'index.php?home='.$_G['controller'].'&act='.$_G['action'];
$_G['message'] = initmessage();
require_once ROOT_PATH.'/'.SUPERADMIN_DIR.'/controls/'.$controller.'.class.php';

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

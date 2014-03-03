<?php
/* for rewrite or iis rewrite */

if (isset($_SERVER['HTTP_X_ORIGINAL_URL'])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
} else if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
}
if($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/index.php' || $_SERVER['REQUEST_URI'] == '/index.php?home=index') {
	$_SERVER['REQUEST_URI'] = '/index.php?home=index&act=index';
}
/* end */
ob_get_clean();
ob_start();
@header('Content-Type: text/html; charset=utf-8');
define('ROOT_PATH',str_replace('\\','/',substr(dirname(__FILE__),0,-3)));
define('IN_SYSTEM',1);
define('TEMPLATEID','2');
define('COMPILEDIR','compile');
define('SESSION_DB',1);
define('DEBUG',1);
if(DEBUG == 1) {
	error_reporting(E_ALL);
	ini_set("error_display", "On");
} else {
	error_reporting(E_ALL^E_NOTICE^E_WARNING);
	ini_set("error_display", "Off");
}

require(ROOT_PATH.'/data/config.inc.php');
require(ROOT_PATH.'./data/config.default.php');
require(ROOT_PATH.'./data/ArrayData.php');
require(ROOT_PATH.'/inc/mysql.class.php');
require(ROOT_PATH.'/inc/global.func.php');
require(ROOT_PATH."/data/lang/global.lang.php");
//初始化数据连接//---------------------------------------------------------
$db = new mysql();
$db->connect($dbhost,$dbuser,$dbpw,$dbname);
$_G['mobile'] = checkmobile(); 
//if(checkmobile()) {
if(0) {
	define('STYLEID','MB');
	define('TPLDIR','mobile');
} else {
	define('STYLEID','ZH');
	define('TPLDIR','default');
}
define("SITE_ROOT",$setting['site_url']);
define('VERSION','VLIFE_1.0');

$tplrefresh = 0;
require(ROOT_PATH.'/inc/template.func.php');

$timezone = 'Asia/Shanghai';
date_default_timezone_set($timezone);
$_G['timestamp'] = time();
$_G['date'] = date("Y-m-d H:i:s", $_G['timestamp']);
$GLOBALS['language'] = $lang;
$_G['BREAD_HOME'] = array('text' => lang('Home'), 'href' => '.');


?>
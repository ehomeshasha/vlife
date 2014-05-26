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
//if(DEBUG == 1) {
if(0) {
	error_reporting(E_ALL);
	ini_set("error_display", "On");
} else {
	error_reporting(E_ALL^E_NOTICE^E_WARNING);
	ini_set("error_display", "Off");
}

require_once(ROOT_PATH.'/data/config.inc.php');
require_once(ROOT_PATH.'./data/config.default.php');
require_once(ROOT_PATH.'./data/ArrayData.php');
require_once(ROOT_PATH.'/inc/mysql.class.php');
require_once(ROOT_PATH.'/inc/global.func.php');
require_once(ROOT_PATH."/data/lang/global.lang.php");
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
define("SITE_ROOT", $_G['siteurl']);
define('VERSION','VLIFE_1.0');

$tplrefresh = 0;
require_once(ROOT_PATH.'/inc/template.func.php');

$timezone = 'Asia/Shanghai';
date_default_timezone_set($timezone);
$_G['timestamp'] = time();
$_G['date'] = date("Y-m-d H:i:s", $_G['timestamp']);
$GLOBALS['language'] = $lang;
$_G['BREAD_HOME'] = array('text' => lang('Home'), 'href' => '.');
$_G['SUPER_BREAD_HOME'] = array('text' => lang('Home'), 'href' => 'index.php?home=user');
$_G['ADMIN_BREAD_HOME'] = array('text' => lang('Home'), 'href' => 'index.php?home=foodorder_company');



/*---------------------------readcache-----------------------------*/
//category
if(!checkfile('category',0)) {
	$categoryArr = $GLOBALS['db']->fetch_all("SELECT * FROM ".tname('category')." WHERE 1 ORDER BY displayorder ASC, cid ASC");
	foreach($categoryArr as $value) {
		$_G['category'][$value[cid]] = $value;
	}
	write('category',$_G['category']);
}else{
	$_G['category']=read('category');
}

if(!checkfile('categorytree',0)) {
	
	$_G['categorytree'] = get_categorytree();
	write('categorytree',$_G['categorytree']);
}else{
	$_G['categorytree']=read('categorytree');
}

$_G['categorytree_merge'] = array();
foreach($_G['categorytree'] as $v) {
	$_G['categorytree_merge'] = array_merge($_G['categorytree_merge'], $v);
}














?>
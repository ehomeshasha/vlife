<?php

if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

define('SYSTEM_KEY', 'vlife_developed_by_zzy');  //用于生成登陆动态密钥
define('CHARSET','utf-8'); //用于cutstr函数
define('COOKIE_EXPIRE', 86400); //登录COOKIE的过期时间
define('UPLOAD_DIR', '/data/upload/'); //设置上传目录路径
$_G['setting']['userlevel'] = array(
	'superadmin' => 9,	//设置超级管理员userlevel
	'company' => 6,	//设置商家userlevel
	'custom' => 1,	//设置普通客户userlevel
);
$_G['setting']['status'] = array(
	'wait' => 0,
	'accept' => 1,
	'reject' => -1,
);
$_G['setting']['order_status'] = array(
	'wait' => 0,
	'pending' => 1,
	'failed' => 3,
	'success' => 4,
);
$_G['setting']['perpage'] = 20; //设置每页显示条数
$_G['setting']['company_id'] = 1; //设置默认的company
$_G['siteurl'] = "http://www.boerka123.com/vlife/"; //设置域名,结尾要加/
$_G['setting']['date_short_format'] = "y-n-j";
$_G['setting']['time_short_format'] = "H:i:s";

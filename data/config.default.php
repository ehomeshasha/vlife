<?php

if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

define('SYSTEM_KEY', 'vlife_developed_by_zzy');  //用于生成登陆动态密钥
define('CHARSET','utf-8'); //用于cutstr函数
define('COOKIE_EXPIRE', 86400); //登录COOKIE的过期时间
define('UPLOAD_DIR', '/data/upload/'); //设置上传目录路径
$_G['setting']['userlevel']['superadmin'] = 9; //设置超级管理员userlevel
$_G['setting']['userlevel']['company'] = 6; //设置超级管理员userlevel
$_G['setting']['userlevel']['custom'] = 1; //设置普通客户userlevel
$_G['setting']['perpage'] = 20; //设置每页显示条数
$_G['setting']['company_id'] = 1; //设置默认的company
$_G['siteurl'] = "http://localhost/vlife/"; //设置域名,结尾要加/
$_G['setting']['date_short_format'] = "y-n-j";
$_G['setting']['time_short_format'] = "H:i:s";

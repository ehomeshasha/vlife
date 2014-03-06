<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}
//cookie处理类
class cookies {
	
	//edit by clear
	//php 5构造函数写法
	function __construct() {
		
	}

	//php4的构造函数
	//function cookies() {}

	//设置cookie
	function set($val) {
		foreach($val as $k=>$v) {
			$this->destroy($k);
			setcookie($k,$v,time()+COOKIE_EXPIRE);
		}
	}
	
	//获取cookie
	function get($key) {
		return $_COOKIE[$key];
	}
	
	//删除cookie
	function destroy($val) {
		if (is_array($val)) {
			foreach ($val as $k => $v) {
				echo $k;
				setcookie($k,$v,-1);
			}
		} else {
			setcookie($val,'',-1);
		}
	}
}
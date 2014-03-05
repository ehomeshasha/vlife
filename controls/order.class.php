<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class order_controller {

	//构造函数
	public function __construct() {
		include ROOT_PATH.'./models/common.php';
		$this->company = new common('company');
		$this->dishes = new common('dishes');
		$this->users = new common('users');
	}
	
	public function index_action() {
		global $_G;
		
		
		if($_POST['submit'] != "true") {
		
			$foodArr = array();
			//$food_ids = $food_counts = "";
			$ids_str = "";
			
			foreach($_COOKIE as $k=>$v) {
				if(preg_match("/^food_count(\d+)$/", $k, $matches)) {
					$food_id = $matches[1];
					$ids_str .= ",'$food_id'";
					/*
					$sql_food="select food_id,food_name,food_pic,food_price,food_intro from qiyu_food WHERE food_id='$food_id' LIMIT 1";
					$rs_food=mysql_query($sql_food);
					$row=mysql_fetch_assoc($rs_food);
					$row['food_count'] = $v;
					$row['food_totalprice'] = intval($v) * floatval($row['food_price']);*/
					$foodArr[$food_id] = array('food_count' => $v);
					//$food_ids .= ",".$food_id;
					//$food_counts .= ",".$v;
				}
			}
			$ids_str = substr($ids_str, 1);
			$dishes = $GLOBALS['db']->fetch_all("SELECT * FROM ".tname('dishes')." WHERE id IN ($ids_str)");
			foreach($dishes as $val) {
				$val['food_totalprice'] = intval($foodArr[$val['id']]['food_count']) * floatval($val['price']);
				$foodArr[$val['id']] = array_merge($foodArr[$val['id']], $val);
				$filepatharr = explode(",", $val['filepath']);
				$patharr = explode("^", $filepatharr[0]);
				$foodArr[$val['id']]['path'] = $patharr[1];
				$company_id = $val['uid'];	
			}
			//echo '<pre>';
			//print_r($foodArr);
			
			if(empty($foodArr)) {
				$msg = lang("Please select dishes first");
				$_SESSION['message'] = array('code' => '1', 'content' => array(lang($msg)));
				$_G['message'] = initmessage();
				$menu = new menu_controller();
				$menu->index_action();
				exit;
			}
			
			$food_str = json_encode($foodArr);
			$csrf = $GLOBALS['session']->get_csrf();
			include template('order');
		} else {
			$GLOBALS['session']->csrfguard_start();
			
			$phone = chkLength("Telephone", getgpc('phone'), 0, 30);
			$address = chkLength("Your address", getgpc('address'), 0, 255);
			$food_str = getgpc('food_str');
			validate_start();
			
			$foodArr = json_decode($food_str, true);
			$totalprice = 0.0;
			foreach($foodArr as $food) {
				$totalprice += floatval($food['food_totalprice']);
			}
			
			$user_info = $GLOBALS['db']->fetch_first("SELECT * FROM ".tname('user_info'). " WHERE phone='$phone'");
			if(empty($user_info)) {
				$credits = $totalprice;
				$password = md5($phone);
				$GLOBALS['db']->query("INSERT INTO ".tname('users')." 
				(username,password,userlevel,dateline) VALUES 
				('$phone','$password',1,$_G[timestamp])");
				$uid = $GLOBALS['db']->insert_id();
				$GLOBALS['db']->query("INSERT INTO ".tname('user_info')." 
				(uid,phone,address,credits) VALUES 
				('$uid','$phone','$address','$credits')");
			} else {
				$uid = $user_info['uid'];
				$credits = floatval($user_info['credits']) + $totalprice;
				$GLOBALS['db']->query("UPDATE ".tname('user_info')." SET phone='$phone',address='$address',credits='$credits' WHERE uid='$uid'");
			}
			
			
			$GLOBALS['db']->query("INSERT INTO ".tname('orders')." 
				(uid,company_id,order_number,phone,address,dishes,totalprice,status,dateline,app) VALUES 
				('$uid','$company_id','$order_number','$phone','$address','$food_str','$totalprice',0,'$_G[timestamp]','foodorder')");
			
			$msg = "Your Order is created successfully";
			$_SESSION['message'] = array('code' => '1', 'content' => array(lang($msg)));
			
			header('Location: index.php?home=setting');
			
		}
	}
	
	
}

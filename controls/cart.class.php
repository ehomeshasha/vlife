<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class cart_controller {

	//构造函数
	public function __construct() {
		include_once ROOT_PATH.'./models/common.php';
		$this->company = new common('company');
		$this->dishes = new common('dishes');
		$this->users = new common('users');
	}
	
	public function index_action() {
		global $_G;
		
		
		if($_POST['submit'] != "true") {
		
			$foodArr = array();
			$ids_str = "";
			
			foreach($_COOKIE as $k=>$v) {
				if(preg_match("/^food_count(\d+)$/", $k, $matches)) {
					$food_id = $matches[1];
					$ids_str .= ",'$food_id'";
					$foodArr[$food_id] = array('food_count' => $v);
				}
			}
			if(empty($foodArr)) {
				$msg = lang("Please select dishes first");
				$_SESSION['message'] = array('code' => '-1', 'content' => array(lang($msg)));
				header("Location: index.php?home=menu");
			}
			$ids_str = substr($ids_str, 1);
			$dishes = $GLOBALS['db']->fetch_all("SELECT * FROM ".tname('dishes')." WHERE id IN ($ids_str)");
			foreach($dishes as $val) {
				$val['food_totalprice'] = intval($foodArr[$val['id']]['food_count']) * floatval($val['price']);
				$foodArr[$val['id']] = array_merge($foodArr[$val['id']], $val);
				$filepatharr = explode(",", $val['filepath']);
				$patharr = explode("^", $filepatharr[0]);
				$foodArr[$val['id']]['path'] = $patharr[1];
			}
			//echo '<pre>';
			//print_r($foodArr);
			$food_str = json_encode($foodArr);
			$csrf = $GLOBALS['session']->get_csrf();
			include_once template('cart');
		} else {
			$GLOBALS['session']->csrfguard_start();
			
			$phone = chkLength("Telephone", getgpc('phone'), 0, 30);
			$address = chkLength("Your address", getgpc('address'), 0, 255);
			$food_str = $_POST['food_str'];
			validate_start();
			
			$foodArr = json_decode($food_str, true);
			$totalprice = 0.0;
			foreach($foodArr as $food) {
				$company_id = $food['company_id'];
				$totalprice += floatval($food['food_totalprice']);
			}
			$user_data = array(
				'phone' => $phone,
				'address' => $address,
				'credits' => $totalprice,
			);
			print_r($user_data);
			$uid = user_save($user_data);
			
		
			$order_id = orderId();
			$order_key = ''; 
			$GLOBALS['db']->query("INSERT INTO ".tname('orders')." 
				(uid,company_id,order_id,order_key,phone,address,dishes,totalprice,status,dateline,app) VALUES 
				('$uid','$company_id','$order_id','$order_key','$phone','$address','$food_str','$totalprice',0,'$_G[timestamp]','foodorder')");
			
			$msg = "Your Order is created successfully";
			$_SESSION['message'] = array('code' => '1', 'content' => array(lang($msg)));
			
			header('Location: index.php?home=order');
			
		}
	}
	
	
}

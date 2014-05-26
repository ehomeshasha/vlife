<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class misc_controller {

	public function __construct() {
		
	}
	public function index_action() {
				
	}
	public function clear_session_action() {
		global $session;
		$session->destroy('message');
	}
	public function cancel_upload_action() {
		global $_G;
		$table_name = getgpc('table_name');
		$id = getgpc('id');
		$path = getgpc('path');
		
		$record = $GLOBALS['db']->fetch_first("SELECT uid,filepath FROM ".tname($table_name)." WHERE id='$id'");
		if($_G['userlevel'] != $_G['setting']['userlevel']['superadmin'] && $_G['uid'] != $record['uid']) {
			showmessage("You'v no right to cancel this uploaded file");	
		}
		$filepathArr = explode(",", $record['filepath']);
		print_r($filepathArr);
		foreach ($filepathArr as $v) {
			if($v == $path) continue;
			$filepathArr2[] = $v;
		}
		$GLOBALS['db']->query("UPDATE ".tname($table_name)." SET filepath='$filepath2' WHERE id='$id'");
		$pathArr = explode("^", $path);
		unlink(ROOT_PATH.$pathArr[1]);
	}
	
	public function get_code_action() {
		global $_G;
		$security_code = random(6);
		echo $security_code;
	}
	
	public function get_orderstatus_action() {
		global $_G;
		$id = getgpc('id');
		$order = $GLOBALS['db']->fetch_first("SELECT status FROM ".tname('orders'). "WHERE id='$id'");
		if($order['status'] == 0 || $order['status'] == 1) {
			$str = "";
		} elseif($order['status'] == 3) {
			$str = "<strong class='text-danger'>UnPrinted</strong>";
		} elseif($order['status'] == 4) {
			$str = "<strong class='text-success'>printed</strong>";
		}
		echo $str;
	}
	
	
	public function get_recommend_dishes_action() {
		global $_G;
		$uuid = getgpc('uuid');
		//$uuid = "b9407f30-f5f8-466e-aff9-25556b57fe6d";
		$beacon = $GLOBALS['db']->fetch_first("SELECT url,dish_ids FROM ".tname('beacon'). "WHERE uuid='$uuid'");
		
		if(empty($beacon)) {
			exit;
		}
		
		if(!empty($beacon['url'])) {
			
			
			if(strpos($beacon['url'], "dealsaccess.ca") === false || strpos($beacon['url'], "ibeacon=true") === false) {
				header("Content-type: text/html");
				echo $beacon['url'];
			} else {
				header("Content-type: text/json");
				$output = curl($beacon['url']);
			}
			echo $output;
			exit;
			
			//echo $beacon['url'];
			
			
			
		}
		
		$dish_ids = dimplode(explode(",", $beacon['dish_ids']));
		$dish_list = $GLOBALS['db']->fetch_all("SELECT * FROM ".tname('dishes')." WHERE id IN ($dish_ids)");
		
		$this->generate_dishes_xml($dish_list);
	}
	
	public function generate_dishes_xml($dishes) {
		//include_once ROOT_PATH."./inc/XML2Array.php";
		include_once ROOT_PATH."./inc/Array2XML.php";
		/*
		$xml = file_get_contents("test.xml");
		$array = XML2Array::createArray($xml);  
		echo '<pre>';
		print_r($array);*/
		
		/*
		$books = array(  
        '@attributes' => array(  
            'type' => 'fiction'  
        ),  
        'book' => array(  
            array(  
                '@attributes' => array(  
                    'author' => 'George Orwell'  
                ),  
                'title' => '1984'  
            ),  
            array(  
                '@attributes' => array(  
                    'author' => 'Isaac Asimov'  
                ),  
                'title' => array('@cdata'=>'Foundation'),  
                'price' => '$15.61'  
            ),  
            array(  
                '@attributes' => array(  
                    'author' => 'Robert A Heinlein'  
                ),  
                'title' =>  array('@cdata'=>'Stranger in a Strange Land'),  
                'price' => array(  
                    '@attributes' => array(  
                        'discount' => '10%'  
                    ),  
                    '@value' => '$18.00'  
                )  
            )  
        )  
    );  */
		
		
		
		$target_array = array();
		foreach($dishes as $key=>$dish) {
			$filepath = $dish['filepath'];
			$path = explode("^", $filepath);
			$target_array['dish'][$key] = array(
				'name' => array('@cdata'=>$dish['name']),
				'description' => array('@cdata'=>$dish['description']) ,
				'price' => $dish['price'],
				'filepath' => htmlspecialchars($path[1]),
			);
			
			
			
			
			
		}
		header("Content-type: text/xml");
		//echo '<pre>';
		//print_r($target_array);
		$xml = Array2XML::createXML('dishes', $target_array);
		echo $xml->saveXML();  
		
		
	}
	
}

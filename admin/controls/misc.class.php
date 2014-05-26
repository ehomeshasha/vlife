<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class misc_controller {

	public function __construct() {
		
	}
	
	public function select_restaurant_action() {
		global $_G;
		$current_sel = getgpc('current_sel');
		$_SESSION['company_id'] = $current_sel; 
	}
	
	public function get_dishes_option_action() {
		global $_G;
		$company_id = getgpc('company_id');
		$dishes = $GLOBALS['db']->fetch_all("SELECT * FROM ".tname('dishes')." WHERE company_id='$company_id'");
		$html = "";
		foreach($dishes as $d) {
			$html .= "<option value='$d[id]'>$d[name]</option>";
		}
		echo $html;
	}
}
?>
<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}
class Printer {
	
	private $order;
	private $format_string;
	
	public function __construct($order) {
		$this->order = $order;
		$this->format_string = $this->format();
	}
	
	public function get_format_string() {
		return $this->format_string;
	}
	
	public function do_print() {
		echo $this->format_string;
	}
	
	
	
	public function Write() {
		
		
		
		
		
	}
	
	public function format() {
		/**
		 * 
		  #14030617310023*Seafood Supreme;1;34;*154.65*15:47 03-08-10*008612345678*发的萨芬fdsdf fdsafdsaf fdsafdsafsa fdsafdsa #
		*/
		$o = $this->order;
		
		$refines = array('order_id', 'totalprice', 'dateline', 'phone', 'address'); 
		foreach($o as $k=>$v) {
			if(in_array($k, $refines)) {
				$o[$k] = preg_replace("/\*;#/", " ", $v);
				$o[$k] = str_replace(array("\r\n", "\r", "\n"), "%%", $o[$k]);
			}
		}
		
		$str = "#".$o['order_id']."*";
		//dishes
		//{"1":{"food_count":"1","id":"1","cid":"6","uid":"3","company_id":"1","name":"fresh Florida stone crab","price":"20.85","filepath":"fresh Florida stone crab.jpg^data/upload/uid_3/1601001q1lrxee1x8xec1d.jpg","description":"fresh Florida stone crab","displayorder":"0","createtime":"1394005776","updatetime":"1394077489","food_totalprice":20.85,"path":"data/upload/uid_3/1601001q1lrxee1x8xec1d.jpg"},"11":{"food_count":"1","id":"11","cid":"5","uid":"3","company_id":"1","name":"Seafood Supreme","price":"34","filepath":"Seafood Supreme.jpg^data/upload/uid_3/160949qsgb8sb9g2ocrv2q.jpg","description":"Seafood Supreme","displayorder":"0","createtime":"1394006991","updatetime":"1394006991","food_totalprice":34,"path":"data/upload/uid_3/160949qsgb8sb9g2ocrv2q.jpg"},"12":{"food_count":"1","id":"12","cid":"5","uid":"3","company_id":"1","name":"Delicious Bacon","price":"65.3","filepath":"Delicious Bacon.jpg^data/upload/uid_3/161022omknsfcnqmmxysnv.jpg","description":"Delicious Bacon","displayorder":"0","createtime":"1394007023","updatetime":"1394007023","food_totalprice":65.3,"path":"data/upload/uid_3/161022omknsfcnqmmxysnv.jpg"},"5":{"food_count":"3","id":"5","cid":"13","uid":"3","company_id":"1","name":"Bolognaise","price":"11.5","filepath":"Bolognaise.jpg^data/upload/uid_3/160526eaorgpeep5oae6lr.jpg","description":"Bolognaise","displayorder":"0","createtime":"1394006727","updatetime":"1394006727","food_totalprice":34.5,"path":"data/upload/uid_3/160526eaorgpeep5oae6lr.jpg"}}
		$dishes = json_decode($o['dishes'], true);
		//echo '<pre>';
		//print_r($dishes);
		foreach($dishes as $d) {
			//print_r($d);
			$d['name'] = preg_replace("/\*;#/", " ", $d['name']);
			$d['food_count'] = preg_replace("/\*;#/", " ", $d['food_count']);
			$d['food_totalprice'] = preg_replace("/\*;#/", " ", $d['food_totalprice']);
			$d['name'] = str_replace(array("\r\n", "\r", "\n"), "%%", $d['name']);
			$d['food_count'] = str_replace(array("\r\n", "\r", "\n"), "%%", $d['food_count']);
			$d['food_totalprice'] = str_replace(array("\r\n", "\r", "\n"), "%%", $d['food_totalprice']);
			$str .= $d['name'].";".$d['food_count'].";".$d['food_totalprice'].";";
		}
		$str .= "*".$o['totalprice']."*".date("H:i m-d-y", $o['dateline'])."*".$o['phone']."*".$o['address']."#";
		
		return $str;
	}
}
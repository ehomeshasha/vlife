<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class order_controller {

	//构造函数
	public function __construct() {
		include_once ROOT_PATH.'./models/common.php';
		$this->company = new common('company');
		$this->dishes = new common('dishes');
		$this->users = new common('users');
	}
	
	public function index_action() {
		global $_G;
		$orders = $GLOBALS['db']->fetch_all("SELECT * FROM ".tname('orders')." WHERE uid='$_G[uid]' ORDER BY dateline DESC");
		include_once template('order');
	}
	
	public function view_action() {
		global $_G;
		$id = getgpc('id');
		$order = $GLOBALS['db']->fetch_first("SELECT * FROM ".tname('orders')." WHERE id='$id'");
		$dishes = json_decode($order['dishes'], true);
		
		include_once template('order_view');
	} 
	
	
	public function print_action() {
		header('Content-type: text/plain');
		global $_G;
		
		$opt = selectOpt(getgpc('opt'), array('print_order','callback'));
		/**
		 * order status
		 * 0 - 等待打印
		 * 1 - 正在打印
		 * 3 - 打印失败
		 * 4 - 打印成功
		 */
		/* for testing
		$testfile = ROOT_PATH."./order/test_$opt.txt";
		$fp0 = fopen($testfile, 'wb');
		fwrite($fp0, json_encode($_GET));
		fclose($fp0);*/
		
		$a = getgpc('a');
		$where = " AND company_id='$a'";
		
		
		$lockfile = ROOT_PATH."./order/.lock";
		if($opt == "print_order") {
			
			if(!file_exists($lockfile)) {
				
				if($_POST['print_type'] == "manual") {
					$id = getgpc('id');
					$sql = "SELECT * FROM ".tname('orders')." WHERE id='$id' AND status={$_G['setting']['order_status']['wait']} {$where}";
					
				} else {
					$sql = "SELECT * FROM ".tname('orders')." WHERE status={$_G['setting']['order_status']['wait']} {$where} ORDER BY dateline ASC LIMIT 1";
				}
				$order = $GLOBALS['db']->fetch_first($sql);	//取出要打印的order数据
				if(empty($order)) {	//没有符合要求的order,则不进行打印
					exit;
				}
				while(true) {
					if(($fp = fopen($lockfile, 'wb')) && fclose($fp)) {	//创建文件锁
						break;
					}
				}
				$GLOBALS['db']->query("UPDATE ".tname('orders')." SET status={$_G['setting']['order_status']['pending']} WHERE id='$order[id]' {$where}");	//更新该条order的状态为正在打印
				
				
				include ROOT_PATH."./class/print.class.php";
				
				$pt = new Printer($order);
				/* for testing
				$str = $pt->get_format_string();
				$fp2 = fopen(ROOT_PATH."./order/order.txt", 'wb');
				fwrite($fp2, $str);
				fclose($fp2);*/
				$pt->do_print();
				
			}
		
		} elseif($opt == 'callback') {
			/* for testing
			$testfile = ROOT_PATH."./order/test.txt";
			$fp = fopen($testfile, 'wb');
			fwrite($fp, json_encode($_GET));
			fclose($fp);*/
			$order_id = getgpc('o');
			$return_ak = getgpc('ak');
			$return_m = getgpc('m');
			$return_dt = strtotime(date("Y-m-d")." ".getgpc('dt').":00");
			if($_GET['ak'] == "Accepted" && $_GET['m'] == "OK") {	//打印订单成功
				$status = $_G['setting']['order_status']['success'];
			} else {	//打印订单失败
				$status = $_G['setting']['order_status']['failed'];
			}
			$GLOBALS['db']->query("UPDATE ".tname('orders')." SET status='$status',return_ak='$return_ak',return_m='$return_m',return_dt='$return_dt' WHERE order_id='$order_id' {$where}");	//更新order状态
			
			while(true) {
				if(unlink($lockfile)) {	//取消文件锁
					break;		
				}
			}
		}		
	}
	
	
}
?>
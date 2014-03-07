<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}
function get_br($val) {
	return preg_replace("/\r{0,1}\n/", "<br />", $val);
}
function logout() {
	include_once ROOT_PATH."./controls/login.class.php";
	$login = new login_controller();
	$login->logout_action();
}
function user_save($data) {
	global $_G;
	$phone = $data['phone'];
	$address = $data['address'];
	$credits = $data['credits'];
	$contactname = $data['contactname'];
	$weixin = $data['weixin'];
	$password = md5($phone);
	
	$user_info = $GLOBALS['db']->fetch_first("SELECT * FROM ".tname('user_info'). " WHERE phone='$phone'");
	if(empty($user_info)) {
		$GLOBALS['db']->query("INSERT INTO ".tname('users')." 
		(username,password,userlevel,dateline) VALUES 
		('$phone','$password',1,$_G[timestamp])");
		$uid = $GLOBALS['db']->insert_id();
		$GLOBALS['db']->query("INSERT INTO ".tname('user_info')." 
		(uid,contactname,weixin,phone,address,credits) VALUES 
		('$uid','$contactname','$weixin','$phone','$address','$credits')");
		
		
		$_POST['username'] = $_POST['password'] = trim($_COOKIE['telephone']);
		include_once ROOT_PATH.'./controls/login.class.php';
		$login = new login_controller();
		$login->login_action(1);
		
		
	} else {
		$uid = $user_info['uid'];
		$credits = floatval($user_info['credits']) + floatval($credits);
		$GLOBALS['db']->query("UPDATE ".tname('user_info')." 
		SET contactname='$contactname',weixin='$weixin',
			phone='$phone',address='$address',credits='$credits' WHERE uid='$uid'");
	}
	return $uid;
}



function orderId($length=4) {
    $date = date('ymdHi');
    $oid_filename = ROOT_PATH.'./data/cache/tmp.txt';

    $oid = @file_get_contents($oid_filename);

    if($oid >= str_pad('9',$length,'9')) {
        $oid = 1;
    } else {
        $oid+=1;
    }

    file_put_contents($oid_filename,$oid);
    $oid = str_pad($oid,$length,'0',0);
    return $date.$oid;
}
function check_company_exists($app='foodorder', $name='restaurant', $url='index.php') {
	global $_G;
	$company = $GLOBALS['db']->fetch_first("SELECT COUNT(*) AS count FROM ".tname('company')." WHERE uid='$_G[uid]' AND app='$app'");
	if(empty($company['count'])) {
		$msg = "Please create new $name first";
		$_SESSION['message'] = array('code' => '-1', 'content' => array(lang($msg)));
		header('Location: '.$url);
		exit;
	}
}
function init_restaurant($company_id = "") {
	global $_G;
	$Arr = $GLOBALS['db']->fetch_all("SELECT id,name FROM ".tname('company')." WHERE uid='$_G[uid]' ORDER BY dateline ASC");
	$html = "";
	if($company_id == "") {
		foreach($Arr as $v) {
			$html .= "<option value='$v[id]'>$v[name]</option>";
		}	
	} else {
		foreach($Arr as $v) {
			$select = $v[id] == $company_id ? "selected='selected'" : ""; 
			$html .= "<option value='$v[id]' $select>$v[name]</option>";
		}
	}
	return $html;
}
function init_categorylist($categoryArr) {
	global $_G;
	$html = "";
	foreach ($categoryArr as $value) {
		if($value['uid'] != $_G['uid']) continue;
		$html .= "<li>
		<p>
		<span>$value[name]</span>
		<a href='".$_G['siteurl'].ADMIN_DIR."/index.php?home=foodorder_category&act=post&opt=edit&cid=$value[cid]'>[edit]</a>
		<a href='javascript:;' class='deletelink' data-uid='$value[uid]' data-id='$value[cid]' data-type='Category' data-href='index.php?home=foodorder_category&act=delete'>[delete]</a></p><ul>";
		$html .= init_categorylist($value['subcate']);
		$html .= "</ul></li>";
	}
	return $html;
}

function get_categorytree($fid = 0, $level = 0 ,$app = '') {
	global $_G;
	$tree = array();
	$level++;
	
	if($app == '') {
		$arr = $GLOBALS['db']->fetch_all("SELECT app FROM ".tname('category')." WHERE 1 GROUP BY app");
		foreach($arr as $value) {
			$tree[$value['app']] = get_categorytree(0, 0, $value['app']);
		}
		return $tree;
	}
	$arr = $GLOBALS['db']->fetch_all("SELECT * FROM ".tname('category')." WHERE app='$app' AND fid=$fid ORDER BY displayorder ASC, cid ASC");
	foreach($arr as $value) {
		$tree[] = array(
				'uid' => $value['uid'],
				'cid' => $value['cid'],
				'fid' => $value['fid'],
				'name' => $value['name'],
				'subcate' => get_categorytree($value[cid], $level, $app),
		);
	}
	
	return $tree;
}
function init_category($categoryArr, $cid = "", $level = 0, $offset = 1, $invalid_count = 0) {
	global $_G;
	$level++;
	$html = "";

	foreach ($categoryArr as $value) {
		if($value['uid'] != $_G['uid']) continue;
		if($offset == 1 && $level <= $invalid_count) {
			$extattr = "disabled='disabled' style='color:#000;'";
		} else {
			$extattr = "";
		}
		$selected = $value[cid] == $cid ? "selected='selected'" : "";
		$html .= "<option value='$value[cid]' $selected $extattr>".str_repeat("&nbsp;", ($level-$offset)*4).$value[name]."</option>";
		$html .= init_category($value['subcate'], $cid, $level, $offset, $invalid_count);
	}

	return $html;
}
function validate_start() {
	global $_G;
	if(!empty($_G['error_msg'])) {
		$_SESSION['message'] = array('code' => '-1', 'content' => $_G['error_msg']);
		header("Location: ".$_SERVER['HTTP_REFERER']);
		exit;
	}
}
function chkNumber($n, $v) {
	global $_G;
	if(empty($v) || !preg_match("/^[1-9]\d*\.{0,1}\d+$/", $v)) {
		$_G['error_msg'][] = lang('Number only for ').$n;
		return false;
	}
	return $v;
}

function chkDigits($n, $v, $min, $max) {
	global $_G;
	if(chkLength($n, $v, $min, $max) === false) {
		return false;
	}
	if(!preg_match("/^\d+$/", $v)) {
		$_G['error_msg'][] = lang('Digit only for ').$n;
		return false;
	}
	return $v;
}
function chkLength($n, $v, $min, $max) {
	global $_G;
	if($min == 0 && empty($v)) {
		$_G['error_msg'][] = $n.lang(' can not be empty'); 
		return false;		
	}
	$len = mb_strlen($v, "UTF-8");
	if($len < intval($min) || $len > intval($max)) {
		$_G['error_msg'][] = lang('The length of ').$n.lang(' is not valid(must ').$min.' < '.$max.')';
		return false;
	}
	return $v;
}


function chkUploadExist($n,$v) {
	global $_G;
	if(is_array($v)) {
		$val = $v[0];
	} else {
		$val = $v;
	}
	if(empty($val)) {
		$_G['error_msg'][] = lang("Please upload ").$n;
		return false;
	}
	if(is_array($v) && count($v) == 1) {
		return $v[0];
	}
	return $v;
}
function login_page($msg) {
	global $_G;
	$_SESSION['message'] = array('code' => '-1', 'content' => array(lang($msg)));
	$_G['message'] = initmessage();
	include_once ROOT_PATH.'./controls/login.class.php';
	$login = new login_controller();
	$login->index_action();
	exit;
}
function admin_login_page($msg) {
	global $_G;
	$_SESSION['message'] = array('code' => '-1', 'content' => array(lang($msg)));
	$_G['message'] = initmessage();
	include_once ROOT_PATH.'./admin/controls/login.class.php';
	$login = new login_controller();
	$login->index_action();
	exit;
}
function superadmin_login_page($msg) {
	global $_G;
	$_SESSION['message'] = array('code' => '-1', 'content' => array(lang($msg)));
	$_G['message'] = initmessage();
	include_once ROOT_PATH.'./superadmin/controls/login.class.php';
	$login = new login_controller();
	$login->index_action();
	exit;
}


function selectOpt($opt, $optArr) {
	if(empty($opt) || !in_array($opt, $optArr)) {
		return $optArr[0];
	}
	return $opt;
}






function get_huanhang($reply) {
	$replyArr = explode("\n", $reply);
	$str = "";
	foreach ($replyArr as $k=>$v) {
		$str .= $v."<br />";
	}
	$str = substr($str, 0, -6);
	return $str;
}
function get_score($score_type, $score_value) {
	if(empty($score_type)) return "";
	return $score_type."({$score_value}分)";
}
function get_date($format, $dateline) {
	if(empty($dateline)) return '';
	return date($format, $dateline);
}
function init_textarea($str) {
	$arr = explode("\n", $str);
	$html = "";
	foreach ($arr as $v) {
		$html .= "<span>$v</span><br />";
	}
	$html = substr($html, 0, -6);
	return $html;
}
function check_blank($data) {
	if(is_array($data)) {
		foreach ($data as $v) {
			if(empty($v)) return false;
		}
	} else {
		if(empty($data)) return false;
	}
	return true;
}
function get_timezone_diff() { 
	$dateTimeZone = new DateTimeZone(date_default_timezone_get()); 
	$dateTime = new DateTime("now"); 
	$dateTimeZoneGMT = new DateTimeZone("Etc/GMT"); 
	$timeOffset = $dateTimeZone->getOffset($dateTime); 
	return $timeOffset; 
} 
//英文版本的日期格式化
function get_abbr_date($timestamp, $timedisplay = false, $dformat = '', $tformat = '', $timeoffset = '9999') {
	global $_G;
	$dformat = empty($dformat) ? $_G['setting']['date_short_format'] : $dformat;
	$tformat = empty($tformat) ? $_G['setting']['time_short_format'] : $tformat;
	$dtformat = $dformat.' '.$tformat;
	$offset = get_timezone_diff();
	$offset = $offset/3600;
	
	$timeoffset = $timeoffset == 9999 ? $offset : $timeoffset;
	$timestamp += $timeoffset * 3600;
	if($timedisplay == false) {
		$s = gmdate($dformat, $timestamp);
	} else {
		$s = gmdate($dtformat, $timestamp);
	}
	
	$todaytimestamp = $_G['timestamp'] - ($_G['timestamp'] + $timeoffset * 3600) % 86400 + $timeoffset * 3600;
	$time = $_G['timestamp'] + $timeoffset * 3600 - $timestamp;
	if($timestamp >= $todaytimestamp) {
		if($time > 3600) {
			return intval($time / 3600).' hours ago';
		} elseif($time > 1800) {
			return 'half hour ago';
		} elseif($time > 60) {
			return intval($time / 60).' minutes ago';
		} elseif($time > 0) {
			return $time.' seconds ago';
		} elseif($time == 0) {
			return 'just now';
		} else {
			return $s;
		}
	} elseif(($days = intval(($todaytimestamp - $timestamp) / 86400)) >= 0 && $days < 0) {
		if($days == 0) {
			if($timedisplay == false) {
				return 'yestoday';
			} else {
				return 'yestoday '.gmdate($tformat, $timestamp);
			}
		} elseif($days == 1) {
			if($timedisplay == false) {
				return 'DBY';
			} else {
				return 'DBY '.gmdate($tformat, $timestamp);
			}
		} else {
			return ($days + 1).' days ago';
		}
	} else {
		return $s;
	}
}
/* 中文版本的日期格式化
function get_abbr_date($timestamp, $timedisplay = false, $dformat = '', $tformat = '', $timeoffset = '9999') {
	global $_G;
	$dformat = empty($dformat) ? $_G['setting']['date_short_format'] : $dformat;
	$tformat = empty($tformat) ? $_G['setting']['time_short_format'] : $tformat;
	$dtformat = $dformat.' '.$tformat;
	$offset = get_timezone_diff();
	$offset = $offset/3600;
	
	$timeoffset = $timeoffset == 9999 ? $offset : $timeoffset;
	$timestamp += $timeoffset * 3600;
	if($timedisplay == false) {
		$s = gmdate($dformat, $timestamp);
	} else {
		$s = gmdate($dtformat, $timestamp);
	}
	$lang = lang('date');
	
	$todaytimestamp = $_G['timestamp'] - ($_G['timestamp'] + $timeoffset * 3600) % 86400 + $timeoffset * 3600;
	$time = $_G['timestamp'] + $timeoffset * 3600 - $timestamp;
	if($timestamp >= $todaytimestamp) {
		if($time > 3600) {
			return intval($time / 3600).$lang['hour'].$lang['before'];
		} elseif($time > 1800) {
			return $lang['half'].$lang['hour'].$lang['before'];
		} elseif($time > 60) {
			return intval($time / 60).$lang['min'].$lang['before'];
		} elseif($time > 0) {
			return $time.$lang['sec'].$lang['before'];
		} elseif($time == 0) {
			return $lang['now'].'</span>';
		} else {
			return $s;
		}
	} elseif(($days = intval(($todaytimestamp - $timestamp) / 86400)) >= 0 && $days < 0) {
		if($days == 0) {
			if($timedisplay == false) {
				return $lang['yday'];
			} else {
				return $lang['yday'].gmdate($tformat, $timestamp);
			}
		} elseif($days == 1) {
			if($timedisplay == false) {
				return $lang['byday'];
			} else {
				return $lang['byday'].gmdate($tformat, $timestamp);
			}
		} else {
			return ($days + 1).$lang['day'].$lang['before'];
		}
	} else {
		return $s;
	}
}*/
function fill_tablerow_with_blank($data, $limitcount = 5) {
	if(empty($data)) $data = array('');
	$count = count($data);
	if($count < $limitcount) {
		for($i=0;$i<($limitcount-$count);$i++) {
			array_push($data, '');
		}
	}
	return $data;
}
function get_statistic_date() {
	global $_G;
	$startdate = date("n.1", $_G['timestamp']);
	$enddate = date("n.j", $_G['timestamp']);
	$starttime = strtotime(date("Y-m-01", $_G['timestamp']));
	$endtime = $_G['timestamp'];
	$date = array(
		'startdate' => $startdate,
		'enddate' => $enddate,
		'starttime' => $starttime,
		'endtime' => $endtime
	);
	return $date;
}
function format_score($str) {
	if($str != "") $str = sprintf("%.2f", $str);
	return $str;
}
function get_active_nav() {
	global $_G;
	$value = $_G['controller']."#".$_G['action'];
	preg_match("/^[a-zA-Z0-9]+/", $value, $matches);
	return array(
		'model' => $matches[0],
		'value' => $value,
	);
}
//生成随即数
function random($length, $numeric = 0) {
	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	$hash = '';
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}
function initmessage($location = "") {
	if($location == "front") {
		$red_css = "danger";
	} else {
		$red_css = "error";
	}
	$str = "";
  	if($_SESSION['message']) {
		$message = $_SESSION['message']; 
		$code = $message['code'];
		if($code == -1) {
			$alertclass = 'alert-'.$red_css;
		} elseif($code == 0) {
			$alertclass = 'alert-info';
		} elseif($code == 1) {
			$alertclass = 'alert-success';
		}
		$str .= "<div class='alert $alertclass alert-dismissable' id='message'>
					<button type='button'' class='close clear_session' data-dismiss='alert' aria-hidden='true'>&times;</button>
					<ul style='margin-bottom:0;'>";
		foreach ($message['content'] as $v) {
			$str .= "<li>$v</li>";
		}
		$str .= "</ul></div>";
	}
	return $str;
}
function geticon($userlevel) {
	switch ($userlevel) {
		case '9':
			$icon = 'icon-star';
			break;
		case '2':
			$icon = 'icon-pencil';
			break;
		case '1':
			$icon = 'icon-edit';
			break;
		case '0':
			$icon = 'icon-wrench';
			break;
		default:
			return false;
	}
	return $icon;
}
function initdate($offset = '-1 month') {
	global $_G;
	
	$default_starttime = strtotime($offset, $_G['timestamp']);
	$default_startdate = date("Y-m-d", $default_starttime);
	$default_endtime = $_G['timestamp'];
	$default_enddate = date("Y-m-d", $default_endtime);
	$request_startdate = $_REQUEST['startdate'];
	$request_enddate = $_REQUEST['enddate'];
	$regex = "/^[\d]{4}-[\d]{2}-[\d]{2}$/";
	
	if(empty($request_startdate)) {
		$startdate = $default_startdate;
		$starttime = $default_starttime;
	} else {
		if(!preg_match($regex, $request_startdate)) showresult('开始日期不符合格式');
		$startdate = $request_startdate;
		$starttime = strtotime($startdate);
	}
	if(empty($request_enddate)) {
		$enddate = $default_enddate;
		$endtime = $default_endtime;
	} else {
		if(!preg_match($regex, $request_enddate)) showresult('结束日期不符合格式');
		$enddate = $request_enddate;
		$endtime = strtotime($enddate) + 86399;
	}
	
	$arr = array(
		'startdate' => $startdate,
		'enddate' => $enddate,
		'starttime' => $starttime,
		'endtime' => $endtime
	);
	return $arr;
}
function lang($k) {
	if(empty($GLOBALS['language'][$k])) {
		return $k;
	}
	return $GLOBALS['language'][$k];
}

function showmessage($message, $code = -1, $url = '') {
	$arr = array(
		'code' => $code,
		'message' => $message,
		'url' => $url
	);
	echo json_encode($arr);
	exit;
}
function global_addslashes($str) {
	if (get_magic_quotes_gpc()) {
	    return trim($str);
	}
	return addslashes(trim($str));
}

function uc_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	$ckey_length = 4;

	$key = md5($key ? $key : UC_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}
function cutstr($string, $length, $dot = ' ...') {
	if(strlen($string) <= $length) {
		return $string;
	}

	$pre = chr(1);
	$end = chr(1);
	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

	$strcut = '';
	if(strtolower(CHARSET) == 'utf-8') {

		$n = $tn = $noc = 0;
		while($n < strlen($string)) {

			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}

			if($noc >= $length) {
				break;
			}

		}
		if($noc > $length) {
			$n -= $tn;
		}

		$strcut = substr($string, 0, $n);

	} else {
		for($i = 0; $i < $length; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
	}

	$strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

	$pos = strrpos($strcut, chr(1));
	if($pos !== false) {
		$strcut = substr($strcut,0,$pos);
	}
	return $strcut.$dot;
}
function check_login($model) {
	global $_G,$cookies;
	if(!empty($model)) {
		$encode_str = $cookies->get($model."_system_auth");
	} else {
		$encode_str = $cookies->get("system_auth");
	}
	if(empty($encode_str)) return false;
	$decode_str = uc_authcode($encode_str, 'DECODE', SYSTEM_KEY);
	if(checkuser($decode_str)) {
		return true;
	}
	return false;
}
function checkuser($decode_str) {
	$decode_str_arr = explode("&", $decode_str);
	$uid = urldecode($decode_str_arr[0]);
	$username = urldecode($decode_str_arr[1]);
	$password = urldecode($decode_str_arr[2]);
	if(getcount('users',"uid='$uid' AND username='$username' AND password='$password'") > 0) {
		global $_G;
		$user = $GLOBALS['db']->fetch_first("SELECT * FROM ".tname('users')." WHERE uid='$uid' AND username='$username' AND password='$password'");
		$user_info = $GLOBALS['db']->fetch_first("SELECT * FROM ".tname('user_info')." WHERE uid='$uid'");
		if(!empty($user_info)) {
			$user = array_merge($user, $user_info);	
		}
		$_G['uid'] = $user['uid'];
		$_G['username'] = $user['username'];
		$_G['userlevel'] = $user['userlevel'];
		$_G['userinfo'] = $user;
		return true;
	}
	return false;
}
function getgpc($k, $type='GP') {
	$type = strtoupper($type);
	switch($type) {
		case 'G': $var = &$_GET; break;
		case 'P': $var = &$_POST; break;
		case 'C': $var = &$_COOKIE; break;
		default:
			if(isset($_GET[$k])) {
				$var = &$_GET;
			} else {
				$var = &$_POST;
			}
			break;
	}
	if(isset($var[$k])) {
		if(is_array($var[$k])) {
			foreach ($var[$k] as $v) {
				$arr[] = global_addslashes($v);
			}
			return $arr;
		} else {
			return global_addslashes($var[$k]);
		}
	} else {
		return NULL;
	}
}
//判断是否是手机
function checkmobile() {
	global $_G;
	//$mobile = array();
	static $mobilebrowser_list =array('iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini',
				'ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung',
				'palmsource', 'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser',
				'up.link', 'blazer', 'helio', 'hosin', 'huawei', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource',
				'alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone',
				'iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop',
				'benq', 'haier', '^lct', '320x320', '240x320', '176x220');
	$pad_list = array('pad', 'gt-p1000');

	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);

	if(dstrpos($useragent, $pad_list)) {
		return false;
	}
	if(($v = dstrpos($useragent, $mobilebrowser_list, true))) {
		$_G['$mobile'] = $v;
		return true;
	}
	$brower = array('mozilla', 'chrome', 'safari', 'opera', 'm3gate', 'winwap', 'openwave', 'myop');
	if(dstrpos($useragent, $brower)) return false;

	$_G['mobile'] = 'unknown';
	if($_GET['mobile'] === 'yes') {
		return true;
	} else {
		return false;
	}
}
function dstrpos($string, &$arr, $returnvalue = false) {
	if(empty($string)) return false;
	foreach((array)$arr as $v) {
		if(strpos($string, $v) !== false) {
			$return = $returnvalue ? $v : true;
			return $return;
		}
	}
	return false;
}
//header("content-type:text/html;charset=gbk");
//BootStrap 翻页函数
function multi($num, $perpage, $curpage, $mpurl, $maxpages = 0, $page = 10, $autogoto = FALSE, $simple = FALSE) {

	$lang['prev'] = "«";
	$lang['next'] = "»";
	$dot = '...';
	$multipage = '';
	$mpurl .= strpos($mpurl, '?') !== FALSE ? '&amp;' : '?';

	$realpages = 1;
	$page -= strlen($curpage) - 1;
	if($page <= 0) {
		$page = 1;
	}
	if($num > $perpage) {

		$offset = floor($page * 0.5);

		$realpages = @ceil($num / $perpage);
		$pages = $maxpages && $maxpages < $realpages ? $maxpages : $realpages;

		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}
		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<li><a onclick=\'jumpto("'.$mpurl.'page=1");\' href="javascript:;">1 '.$dot.'</a></li>' : '').
		($curpage > 1 && !$simple ? '<li><a onclick=\'jumpto("'.$mpurl.'page='.($curpage - 1).'");\' href="javascript:;">'.$lang['prev'].'</a></li>' : '<li class="disabled"><a href="javascript:;">'.$lang['prev'].'</a></li>');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? '<li class="active"><a href="javascript:;">'.$i.'</a></li>' :
			'<li><a onclick=\'jumpto("'.$mpurl.'page='.$i.'");\' href="javascript:;">'.$i.'</a></li>';
		}
		$multipage .= ($to < $pages ? '<li><a onclick=\'jumpto("'.$mpurl.'page='.$pages.'");\' href="javascript:;">'.$dot.' '.$realpages.'</a></li>' : '').
		($curpage < $pages && !$simple ? '<li><a onclick=\'jumpto("'.$mpurl.'page='.($curpage + 1).'");\' href="javascript:;">'.$lang['next'].'</a></li>' : '<li class="disabled"><a href="javascript:;">'.$lang['next'].'</a></li>');

		$multipage = $multipage ? '<ul>'.($shownum && !$simple ? '<em>&nbsp;'.$num.'&nbsp;</em>' : '').$multipage.'</ul>' : '';
	}
	$maxpage = $realpages;
	return $multipage;
}

//Add By 张知严   将数组转化为SQL命令字符串
function dimplode($array) {
	if(!empty($array)) {
		return '"'.implode('","', is_array($array) ? $array : array($array)).'"';
	} else {
		return 0;
	}
}
function getcount($tablename,$where) {
	$arr = $GLOBALS['db']->fetch_first("select count(*) as count from ".tname($tablename)." where $where");
	return $arr['count'];
}

//表前缀
function tname($table) {
	global $dbname,$table_prefix;
	return '`'.$dbname.'`.`'.$table_prefix.$table.'`';
}
//读取文件内容
function readf($file)
{
	if(function_exists('file_get_contents'))
	{
		$content=file_get_contents($file);
	}
	else
	{
		$fp=fopen($file,'r');
		while(!feof($fp))
		{
			$content=fgets($fp,1024);
		}
		fclose($fp);
	}
	return $content;
}




//判断表单提交
function submitcheck($submitbutton)
{
	if(empty($_REQUEST[$submitbutton]))
	{
		return false;
	}
	else
	{
		return true;
	}
}
//判断缓存时间
function checkfile($file,$cachetime=60) {	
	if(DEBUG) {
		return false;
	}
	$file=ROOT_PATH.'/data/cache/'.$file.'.php';
	if(is_file($file)) {
		include_once $file;
		if(($writetime+$cachetime)>time()) {
			return true; //不更新文件
		} else {
			return false;  //更新文件
		}
	}
	return false;
}
//写缓存内容
function write($file,$content) {
	$file = ROOT_PATH.'/data/cache/'.$file.'.php';
	if(is_array($content)) {
		$content = var_export($content,1);
	} else {
		$content = 'array()';
	}
	$content = 
	'<?php if(!defined("IN_SYSTEM")){echo "error!";} $writetime='.time().'; $content='.$content.';?>';
	if(function_exists('file_put_contents')) {
		file_put_contents($file,$content);
	} else {
		$fp = fopen($file,'w');
		fwrite($fp,$content);
		fclose($fp);
	}
}
//写文件
function writefile($file,$content)
{
	if(function_exists('file_put_contents'))
	{
		return file_put_contents($file,$content);
	}
	else
	{
		$fp = fopen($file,'w');
		return fwrite($fp,$content);
		fclose($fp);
	}
}
//读文件
function read($file) {
	$file1 = ROOT_PATH.'/data/cache/'.$file.'.php';
	include_once($file1);	
	return $content;
}
//删除文件
function deletef($file)
{
	$file=ROOT_PATH.'/data/cache/'.$file.'.php';
	@unlink($file);
}
//清空缓存
function cleancache($type='php',$mdir='')
{
	$path=$mdir?$mdir:($GLOBALS['cachedir']?$GLOBALS['cachedir']:'data/cache');
	$path=ROOT_PATH.str_replace(ROOT_PATH,'',$path);
	if(!is_writable($path))
	{
		return 'nowrite';
	}
	$dir=scandir($path);
	$nullfile='';
	if($type)
	{
		foreach($dir as $k=>$v)
		{
			$newfile=$path.'/'.$v;
			if($v!='.' && $v!='..' && is_file($newfile))
			{
				if(strpos($newfile,$type))
				{
					$a=unlink($newfile);
					$nullfile.=$newfile;
				}
			}
		}
	}
	else
	{
		foreach($dir as $k=>$v)
		{
			$newfile=$path.'/'.$v;

			if($v!='.' && $v!='..' && is_file($newfile))
			{
				$a=unlink($newfile);
				$nullfile.=$newfile;
			}
		}
	}
	if(empty($nullfile))
	{
		return 'null';
	}
	else
	{
		return $a;
	}
}
//建立目录
function mkdir2($dir)
{
	if(!is_dir(dirname($dir)))
	{
		mkdir2(dirname($dir));
	}
	mkdir($dir);
	chmod($dir,0777);
}
/*生成url*/
function url($home='index',$act='index',$paramer=array())
{
	$url = (substr(SITE_ROOT,-1,1) == '/') ? SITE_ROOT : SITE_ROOT.'/';
	if($GLOBALS['rewrite'])
	{
		if($home != 'index' && !empty($home))
		{
			$urlarr['home'] = str_replace('_','',str_replace('-','',$home));
		}
		if($act != 'index' && !empty($act))
		{
			$urlarr['act'] = str_replace('_','',str_replace('-','',$act));
		}
		if($paramer)
		{
			$p = '';
			foreach($paramer as $k=>$v)
			{
				$urlarr[$k] = str_replace('_','',str_replace('-','',$v));
			}
		}
		if($urlarr)
		{
			foreach($urlarr as $k=>$v)
			{
				$a[] = $k.'_'.$v;
			}
			$urltemp = implode('-',$a);
			$url .= $urltemp.'.html';
		}
	}
	else
	{
		if($home == 'index' && $act == 'index')
		{
			$url .= 'index.php';
		}
		elseif($home == 'index')
		{
			$url .= 'index.php?act='.$act;
		}
		elseif($act=='index')
		{
			$url .= 'index.php?home='.$home;
		}
		else
		{
			$url .= 'index.php?home='.$home.'&act='.$act;
		}
		if($paramer)
		{
			$p = '';
			foreach($paramer as $k=>$v)
			{
				$p .= '&'.$k.'='.$v;
			}
			if(strpos($url,'?'))
			{
				$url .= $p;
			}
			else
			{
				$url .= '?'.substr($p,1);
			}
		}
	}
	return $url;
}
//模版替换函数
function template($file, $templateid = 0, $tpldir = '') {
	$filearr = explode("#", $file);
	if(count($filearr) == 2) {
		$tpldir = $filearr[0]."/views/".TPLDIR;
		$file = $filearr[1];
	} else {
		$tpldir		= $tpldir ? $tpldir : 'views/'.TPLDIR;
	}	
	$tplfile	= ROOT_PATH.'./'.$tpldir.'/'.$file.'.htm';
	$file		== 'header' && CURSCRIPT && $file = 'header_'.CURSCRIPT;
	
	
	$templateid = $templateid ? $templateid : TEMPLATEID;
	
	$filebak = $file;
	$objfile = ROOT_PATH.'./data/'.COMPILEDIR.'/'.STYLEID.'_'.$templateid.'_'.$file.'.tpl.php';
	//echo $objfile;
	if($templateid != 1 && !file_exists($tplfile)) {
		$tplfile = ROOT_PATH.'./views/default/'.$filebak.'.htm';
	}
	checktplrefresh($tplfile, $tplfile, is_file($objfile)?filemtime($objfile):'', $templateid, $tpldir);
	return $objfile;
}

function checktplrefresh($maintpl, $subtpl, $timecompare, $templateid, $tpldir) {

	if(DEBUG) {
		require_once ROOT_PATH.'./inc/template.func.php';
		parse_template($maintpl, $templateid, $tpldir);
		return true;
	}

	global $tplrefresh;

	if(empty($timecompare) || $tplrefresh == 1 || ($tplrefresh > 1 && !($GLOBALS['timestamp'] % $tplrefresh))) {

		if(empty($timecompare) || @filemtime($subtpl) > $timecompare) {
			require_once ROOT_PATH.'./inc/template.func.php';
			parse_template($maintpl, $templateid, $tpldir);
			return TRUE;
		}
	}

	return FALSE;
}
function dreferer($default = '') {

	$referer=isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
	if(strpos('a'.$referer,url('user','login'))) {
		$referer = $default;
	} else {
		$referer = substr($referer, -1) == '?' ? substr($referer, 0, -1) : $referer;
	}
	return $referer;
}
function dhtmlspecialchars($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dhtmlspecialchars($val);
		}
	} else {
		$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1',
		//$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
		str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
	}
	return $string;
}
function strexists($haystack, $needle) {
	return !(strpos($haystack, $needle)===FALSE);
}
function dexit($message)
{
	exit($message);
}
function errorlog($file,$content)
{
	$content='<?php if(!defined("IN_SYSTEM")){?>error<?php }?>';
	if(function_exists('file_put_contents'))
	{
		file_put_contents(ROOT_PAHT.'/data/log/'.$file,$content);
	}
	else
	{
		$fp=fopen($file,'w');
		fwrite($fp,$content);
		fclose($fp);
	}
}
//获取当天的星期（1-7）


function GetWeek($times)
{
    $res = date('w', strtotime($times));
    if($res==0)
       $res=7;
    return $res;
}
//获取当天时间
function GetTime($times)
{
    $res = date('H:i', strtotime($times));
    return $res;
}
//获取现在过几月的的时间
function GetMonth($Month,$type='l')
{
    if(!strcmp($type,'b'))
      $res=date("Y-m-d H:i:s",strtotime("-$Month months"));
    if(!strcmp($type,'l'))
      $res=date("Y-m-d H:i:s",strtotime("+$Month months"));
    return $res;
}
//获取当前时间
function GetCurrentDateTime()
{
    $res=date("Y-m-d H:i:s",time());
    return $res;
}
//获取当前时间隔几小时之前或之后的时间
function GetDiffHours($hours,$type='l')
{
  if(!strcmp($type,'b'))
     $res=date("Y-m-d H:i:s",strtotime("-$hours hour"));
  if(!strcmp($type,'l'))
     $res=date("Y-m-d H:i:s",strtotime("+$hours hour"));
  return $res;     
}
//间隔几分钟之前或之后的时间
function GetDiffMinute($Minute,$type='l')
{
  if(!strcmp($type,'b'))
     $res=date("Y-m-d H:i:s",strtotime("-$Minute minute"));
  if(!strcmp($type,'l'))
     $res=date("Y-m-d H:i:s",strtotime("+$Minute minute"));
  return $res;     
}
//间隔几秒之前或之后的时间
function GetDiffSec($sec,$type='l')
{
  if(!strcmp($type,'b'))
     $res=date("Y-m-d H:i:s",strtotime("-$sec second"));
  if(!strcmp($type,'l'))
     $res=date("Y-m-d H:i:s",strtotime("+$sec second"));
  return $res;     
}

//间隔几个星期之前或之后的时间
function GetDiffWeek($Week,$type='l')
{
  if(!strcmp($type,'b'))
     $res=date("Y-m-d H:i:s",strtotime("-$Week week"));
  if(!strcmp($type,'l'))
     $res=date("Y-m-d H:i:s",strtotime("+$Week week"));
  return $res;     
}
// 间隔几天之间的时间
function GetDiffDays($days,$type='l')
{
  if(!strcmp($type,'b'))
     $res=date("Y-m-d H:i:s",strtotime("-$days day"));
  if(!strcmp($type,'l'))
     $res=date("Y-m-d H:i:s",strtotime("+$days day"));
  return $res;     
}
//间隔几年之前或之后的时间
function GetDiffYears($year,$type='l')
{
  if(!strcmp($type,'b'))
     $res=date("Y-m-d H:i:s",strtotime("-$year year"));
  if(!strcmp($type,'l'))
     $res=date("Y-m-d H:i:s",strtotime("+$year year"));
  return $res;     
}
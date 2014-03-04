<?php 
/**
 *  Author: 张知严    
 *  Data:   2012.11.6
 *  Description: 短信发送函数，在网银或支付宝支付成功后会自动发送短信到用户手机
 */
//短信发送函数
function GetHttpContent($fsock=null) {
			$out = null;
			while($buff = @fgets($fsock, 2048)){
				$out .= $buff;
			}
			fclose($fsock);
			$pos = strpos($out, "\r\n\r\n");
			$head = substr($out, 0, $pos);    //http head
			$status = substr($head, 0, strpos($head, "\r\n"));    //http status line
			$body = substr($out, $pos + 4, strlen($out) - ($pos + 4));//page body
			if(preg_match("/^HTTP\/\d\.\d\s([\d]+)\s.*$/", $status, $matches)){
				if(intval($matches[1]) / 100 == 2){
					return $body;  
				}else{
					return false;
				}
			}else{
				return false;
			}
		}
	
function DoGet($url){
	$url2 = parse_url($url);
	$url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
	$url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
	$host_ip = @gethostbyname($url2["host"]);
	$fsock_timeout = 2;  //2 second
	if(($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $fsock_timeout)) < 0){
		return false;
	}
	$request =  $url2["path"] .($url2["query"] ? "?".$url2["query"] : "");
	$in  = "GET " . $request . " HTTP/1.0\r\n";
	$in .= "Accept: */*\r\n";
	$in .= "User-Agent: Payb-Agent\r\n";
	$in .= "Host: " . $url2["host"] . "\r\n";
	$in .= "Connection: Close\r\n\r\n";
	if(!@fwrite($fsock, $in, strlen($in))){
		fclose($fsock);
		return false;
	}
	return GetHttpContent($fsock);
}

function DoPost($url,$post_data=array()){
	$url2 = parse_url($url);
	$url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
	$url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
	$host_ip = @gethostbyname($url2["host"]);
	$fsock_timeout = 2; //2 second
	if(($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $fsock_timeout)) < 0){
		return false;
	}
	$request =  $url2["path"].($url2["query"] ? "?" . $url2["query"] : "");
	$post_data2 = http_build_query($post_data);
	$in  = "POST " . $request . " HTTP/1.0\r\n";
	$in .= "Accept: */*\r\n";
	$in .= "Host: " . $url2["host"] . "\r\n";
	$in .= "User-Agent: Lowell-Agent\r\n";
	$in .= "Content-type: application/x-www-form-urlencoded\r\n";
	$in .= "Content-Length: " . strlen($post_data2) . "\r\n";
	$in .= "Connection: Close\r\n\r\n";
	$in .= $post_data2 . "\r\n\r\n";
	unset($post_data2);
	if(!@fwrite($fsock, $in, strlen($in))){
		fclose($fsock);
		return false;
	}
	return self::GetHttpContent($fsock);
}


function HttpRequest($url, $data=array(), $abort=false) {
	if ( !function_exists('curl_init') ) { return empty($data) ? DoGet($url) : DoPost($url, $data); }
	$timeout = $abort ? 1 : 2;
	$ch = curl_init();
	if (is_array($data) && $data) {
		$formdata = http_build_query($data);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $formdata);
	}
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	$result = curl_exec($ch);
	return (false===$result && false==$abort)? ( empty($data) ? DoGet($url) : DoPost($url, $data) ) : $result;
}

function sms_send($phone, $content){
	/*
	if (mb_strlen($content, 'UTF-8') < 10) {
		return '短信长度低于10汉字？长点吧～';
	}
	*/
	$user = strval("bestjilei"); 
	$pass = strtolower(md5("wuhandaxue"));
	$content = urlencode($content);
	$api = "http://notice.zuitu.com/sms?user={$user}&pass={$pass}&phones={$phone}&content={$content}";
	$res = HttpRequest($api);
	return trim(strval($res))=='+OK' ? true : strval($res);
}
?>
<?php
session_start();
include("jieshouxiaoxi.php");
define("APPID", "wx1290ee2e8591486b");
define("APPSECRET", "3c1ba8cc9acb69a5d02285f12c4c7062");
$xjson = ' {"button":[
        {
            "type":"view",
            "name":"Home",
            "url":"http://www.boerka123.com/vlife/"
        },
		
		{
           "type":"view",
           "name":"Food Menu",
           "url":"http://www.boerka123.com/vlife/index.php?home=menu"
      	},
      	{
           "type":"view",
           "name":"Orders",
           "url":"http://www.boerka123.com/vlife/index.php?home=order"
      	},
      	{
           "type":"view",
           "name":"Settings",
           "url":"http://www.boerka123.com/vlife/index.php?home=setting"
      	},
      	
		
]}';

/*
 * {
           "type":"view",
           "name":"Food Menu",
           "url":"http://www.boerka123.com/vlife/index.php?home=menu"
      	},
		
		{"name":"My Order",
     	 "sub_button":[
			{
			   "type":"view",
			   "name":"my order",
			   "url":"http://www.boerka123.com/vlife/index.php?home=order"
			},
			{
			   "type":"view",
			   "name":"settings",
			   "url":"http://www.201xian.com/mobile/order.html"
			},
			{
			   "type":"view",
			   "name":"text3",
			   "url":"http://201xian.com/7dianban/1.htm"
			}
		]}
 */

$get_access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET;
if(empty($_SESSION['access_token'])) {
	$return_str = vita_get_url_content($get_access_token_url);
	$return_arr = json_decode($return_str, true);
	$_SESSION['access_token'] = $return_arr['access_token'];
}

//$_SESSION['access_token'] = "MvY6Nbbb53x3I3skM-ctfKZzsrTzV3Yw96LdyABfeKCvwqRAh0sHqIBswk4IBNDgoKDFDMe5RNzgKKjRXLhh677Hn6hdvufLMAAVP0fr0HnmO7yP4VWL-vZ2n4903lCNV5fzqvMeGt8XOATcIys97A";
$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$_SESSION['access_token'];
$result = vpost($url,$xjson);


function vita_get_url_content($href) {
	if(function_exists('file_get_contents')) {
		$file_contents = file_get_contents($href);
	}else {
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $href);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
		$file_contents = curl_exec($ch);
		curl_close($ch);
	}
	return $file_contents;
}


function vpost($url,$data){ // 模拟提交数据函数
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)'); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包x
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
       echo 'Errno'.curl_error($curl);//捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据
}

?>
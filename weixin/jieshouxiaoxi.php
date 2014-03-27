<?php
//接收消息后解析xml

$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
if (!empty($postStr)){
	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
	$ToUserName = $postObj->ToUserName;				//获取开发者微信号
	$FromUserName = $postObj->FromUserName;    		//获取发送方帐号（一个OpenID）       
	$CreateTime = time();							//获取消息创建时间 （整型）
	$MsgType = $postObj->MsgType;					//获取消息类型
}else {
	echo "";
	exit;
}

//回复文本消息
include("wenbenxiaoxi.php");

//关注和取消关注回复
include("guanzhu.php");
?>
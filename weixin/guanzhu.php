<?
 /*关注和取消关注接收到的xml格式
 <xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[FromUser]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[subscribe]]></Event>    事件类型，subscribe(订阅)、unsubscribe(取消订阅)
</xml>
*/

if($MsgType=="event"){
	$Event2 = $postObj->Event;
	$textTpl2 = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<ArticleCount>1</ArticleCount>
					<Articles>
					<item>
					<Title><![CDATA[%s]]></Title> 
					<Description><![CDATA[%s]]></Description>
					<PicUrl><![CDATA[%s]]></PicUrl>
					<Url><![CDATA[%s]]></Url>
					</item>
					</Articles>
					</xml> ";
	$MsgType2 = "news";
	$Title2 = "XXX restaurant！";
	$PicUrl2 = "http://www.boerka123.com/diancan/weixin/welcome.jpg";
	$url2 = "http://www.boerka123.com/diancan/";
	if($Event2=="subscribe"){		
		$Description2 = "Welcome to XXX restaurant！";
		$resultStr2 = sprintf($textTpl2, $FromUserName, $ToUserName, $CreateTime, $MsgType2, $Title2, $Description2, $PicUrl2, $url2);
		echo $resultStr2;
	}else if($Event2=="unsubscribe"){
		$Description2 = "Welcome again next visit！";
		$resultStr2 = sprintf($textTpl2, $FromUserName, $ToUserName, $CreateTime, $MsgType2, $Title2, $Description2, $PicUrl2, $url2);
		echo $resultStr2;
	}else{
		echo "Input something...";
	}
}


?>
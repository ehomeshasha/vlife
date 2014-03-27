<?
/*接收到文本消息的xml
 <xml>
 <ToUserName><![CDATA[toUser]]></ToUserName>			开发者微信号
 <FromUserName><![CDATA[fromUser]]></FromUserName> 		发送方帐号（一个OpenID）
 <CreateTime>1348831860</CreateTime>					消息创建时间 （整型）
 <MsgType><![CDATA[text]]></MsgType>					text
 <Content><![CDATA[this is a test]]></Content>			文本消息内容
 <MsgId>1234567890123456</MsgId>						消息id，64位整型
 </xml>
 */
 
//文本消息回复
if($MsgType=="text"){
	$Content1 = trim($postObj->Content);					//获取文本消息内容
	$MsgId1 = trim($postObj->MsgId);						//获取消息id，64位整型
	
//回复消息
	$textTpl1 = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>								
				<FromUserName><![CDATA[%s]]></FromUserName>							
				<CreateTime>%s</CreateTime>											
				<MsgType><![CDATA[%s]]></MsgType>									
				<Content><![CDATA[%s]]></Content>									
				</xml>";    
				
	if(!empty($Content1))
	{
		switch ($Content1)
		{
			case 1:
				 $contentStr1 = "hello !";
				 break;  
			case 2:
				 $contentStr1 = "How are you !";
				 break;
			case 3:
				$contentStr1 = "How Do You Do !";
				break;
			default:
				$contentStr1 = "That's all !";
			  
		}
		$MsgType1 = "text";
		$resultStr1 = sprintf($textTpl1, $FromUserName, $ToUserName, $CreateTime, $MsgType1, $contentStr1);
		echo $resultStr1;
	}else{
		echo "Input something...";
	}
	
/*
//接入数据库模板
	$pdo = new PDO("mysql:acmesoft.gotoftp3.com; dbname=weixin",'root','');
	$pdo -> query("set names utf8");
	$sql ="insert into weixin(id,touser,fromuser,createTime,content) values('$MsgId','$ToUserName','$FromUserName','$createTime','$content')"; 
	$pdo -> exec($sql);
*/	
	
}
?>
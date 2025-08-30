<?php
require_once ROOT_PATH."/aiapi/text2text.php";
use WebSocket\Client; 
function Ai_config($apicom){
	$apicom="modelscope";
	require ROOT_PATH."/config/ai/apicom_config.php";
	$config=$apicom_config[$apicom];
	return $config;
}
function AIAsk($prompt,$messages=[],$system=""){
	$config=Ai_config("aliyun");
	AiApi\text2text::init($config);
	$has_system=false;
	if(!empty($messages)){
		foreach($messages as $m){
			if($m["role"]=="system"){
				$has_system=true;
				break;
			}
		}
	}
	if(!$has_system){
		if(empty($system)){
			$system=GAME_SYSTEM;
		}
		$oldmsg=$messages;

		$messages=[["role"=>"system","content"=>$system]];
		if(!empty($oldmsg)){
			foreach($oldmsg as $m){
				$messages[]=$m;
			}
		}
	}
	$content=AiApi\text2text::chat($prompt,$messages);
	return $content;
} 
function AIRun($prompt,$messages=[],$system=""){
    $config=Ai_config("aliyun");
	AiApi\text2text::init($config);
	if(!empty($messages)){
		foreach($messages as $m){
			if($m["role"]=="system"){
				$has_system=true;
				break;
			}
		}
	}
	if(!$has_system){
		if(empty($system)){
			$system=GAME_SYSTEM;
		}
		$oldmsg=$messages;

		$messages=[["role"=>"system","content"=>$system]];
		if(!empty($oldmsg)){
			foreach($oldmsg as $m){
				$messages[]=$m;
			}
		}
	}
	 
	//连接ws
	$wsclient = new  Client("wss://wss.deituicms.com:8282/");
		     
		     
	$wstaskid=$_POST["wstaskid"];
	$wsclient_to=$_POST["wsclient_to"];
	
	$res= $wsclient->send(json_encode([
	   "type"=>"login",
		"k"=>"aiapi"
	])); 
	$content=""; 
	$callback=function($ch, $data) use($wsclient,$wsclient_to,$wstaskid,&$content)   {
	  
	 
	  $lines=explode("\n",$data);
	  foreach($lines as $line){
		$ex=explode(":",$line);
		if($ex[0]=='data'){
		 
		  $json=substr($line,5);
		  $arr=json_decode($json,true);
		  if(empty($arr["choices"][0]["delta"]["content"])){
			continue;
		  }
		  $con=$arr["choices"][0]["delta"]["content"];
		  $content.=$con;
	  
		  //发送给客户端
		  $wsclient->send(json_encode([
			"type"=>"say",
			"wsclient_to"=>$wsclient_to,
			"content"=>$con,
			"wstaskid"=>$wstaskid
		  ]));
		   
		   
		  
		}
	  }
	  return strlen($data);
	};
    AiApi\text2text::stream_chat($prompt,$messages,$callback);
	return $content;
} 


 

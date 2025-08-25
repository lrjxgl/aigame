<?php
namespace doubao;
class img2video{
	 
	public static $api_key="sk-2b9";
	public static $model="";
	public static $baseurl="https://ark.cn-beijing.volces.com";
	 
	public static function init($cfg=[]){
		$arr=explode(",",$cfg["doubao_api_key"]);
		shuffle($arr);
		$api_key=$arr[0];
        self::$api_key= $api_key;
		if(!empty($cfg["doubao_img2video_model"])){
			self::$model=$cfg["doubao_img2video_model"];
		}else{
			self::$model='doubao-seaweed-241128';
		}
		 
	}
	public static function curl_get($url){
		$api_key=self::$api_key;
		$ch = curl_init();
		 curl_setopt($ch, CURLOPT_URL, $url);
		 curl_setopt($ch, CURLOPT_HEADER, 0);
		 curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	 
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Bearer '.$api_key,
		));
		 $content= curl_exec($ch);
		 curl_close($ch);
		 return $content;
   } 
	public static function post_json($url, $json,$Async="enable"){
	
		$api_key=self::$api_key;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 	
		curl_setopt($ch, CURLOPT_POST, TRUE); 	
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json); 	
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 	
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 1);	
		$asyncStr="";	
		if($Async=='enable'){	
			$asyncStr='X-DashScope-Async: '.$Async;	
		}
	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(	
			'Content-Type: application/json',	
			'Content-Length: ' . strlen($json),	
			'Authorization: Bearer '.$api_key,
			$asyncStr
		));	
		$ret = curl_exec($ch);
		curl_close($ch);
		return $ret;	
	}
	
	public static function create($ops=[]){	
		 
		 
		$prompt=$ops["prompt"];
        $size="1080x1920";
     
		$with_audio=false;
		if(isset($ops["with_audio"])){
			$with_audio=$ops["with_audio"];
		}
		
		$ops=[
			 
			"model"=>self::$model,
			"content"=>[
				[
					"type"=>"text",
					"text"=>$prompt
				],
				[
					"type"=>"image_url",
					"image_url"=>[
						"url"=>$ops["imgurl"],

					],


				]
			]
			  
		];	
		$res=self::post_json(self::$baseurl."/api/v3/contents/generations/tasks",json_encode($ops),false);
        
		$taskid=""; 
		$arr=json_decode($res,true);
		if(isset($arr["id"])){
			$taskid=$arr["id"];
		}
		
		return $taskid;
	}
	
	public static function checkTask($taskid){
		$url=self::$baseurl."/api/v3/contents/generations/tasks/".$taskid;
		$res=self::curl_get($url);
		 
		$arr=json_decode($res,true);
		 
		$mp4url="";
		$imgurl="";
		$data=[
			"error"=>0
		];
		if(!empty($arr["error"])){
			return [
				"error"=>1,
				"message"=>$arr["error"]
			];
		}
		if(!empty($arr["content"])){
			$mp4url=$arr["content"]["video_url"];
			$imgurl="";
			$data=[
				"mp4url"=>$mp4url,
				"imgurl"=>$imgurl,
				"error"=>0
			];
		}
		return $data;
	}
	
}

 
 

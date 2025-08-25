<?php
namespace AiApi;
class Text2Text{
	public static $class=""; 
	public static $cfg="";
	public static function init($cfg=[]){
		self::$cfg=$cfg;
	 

		if(file_exists(__DIR__."/".$cfg["apicom"]."/text2text.php")){
			require_once __DIR__."/".$cfg["apicom"]."/text2text.php";
			self::$class=$cfg["apicom"]."\\text2text";
			self::$class::init($cfg);
			 
		}else{
			require_once __DIR__."/aliyun/text2text.php";
			self::$class="aliyun\\text2text";
			self::$class::init($cfg);
		}
		
	}
	 
	
	public static function chat($prompt,$messages=[]){	
		$class=self::$class;
		
		return $class::chat($prompt,$messages);
		 
		 
	}
	
	public static function stream_chat($prompt,$messages=[],$callback=null){
		$class=self::$class;
		 
		return $class::stream_chat($prompt,$messages,$callback);
	}
	
}

 
 

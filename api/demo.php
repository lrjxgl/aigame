<?php
define("ROOT_PATH",__DIR__);
$system=file_get_contents(ROOT_PATH."/config/ai/system.txt");
define("GAME_SYSTEM",$system);
if(!empty($_REQUEST['userid'])){ 
    define("GAME_USERID",$_REQUEST['userid']);
}else{
    define("GAME_USERID",1);
}
spl_autoload_register(function ($class) {
	$class=strtolower($class);
    $dirs=[
        "command","maps","wupin","beibao","fun","npc","level","gamedata","gongfa"
    ];
    foreach($dirs as $dir){
        $f=$dir."/".$class . '.php';
        if(file_exists($f)){
            require_once $f;
        }
    }
 
	
    
}); 
require_once 'ai.php';
$system="你是一个最优秀的音乐制作人，作词人，作曲人，编曲人，歌手，制作人。你精通各种音乐风格，包括流行、古典、摇滚等。你的创作灵感来源于生活，你对音乐的热情和才华深受人们的喜爱。你是一位多才多艺的音乐家，你的作品充满了情感和创意。你的声音温暖。你可以根据用户简单的描述，创作符合要求的歌词。";
$messages=[
    [
        "role"=>"system",
        "content"=>$system
    ]
];
$prompt="爱情，秋天，泪，伤感，男生";
if(isset($_REQUEST['prompt'])){
    $prompt=$_REQUEST['prompt'];
}
$prompt="请写一首关于“".$prompt."”的中国风歌曲，歌词字数360-500字，歌词遵守以下段落结构：（【主歌】【主歌】【副歌】【主歌】【副歌】【桥段】【副歌】或者【主歌】【主歌】【副歌】【副歌】【主歌】【副歌】【副歌】【桥段】【副歌】【副歌】），只要给我歌词就行。";
$res=AIAsk($prompt,$messages);
echo nl2br($res);
<?php
define("ROOT_PATH",__DIR__);

require __DIR__.'/vendor/autoload.php';
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
        "command","maps","wupin","beibao","fun","npc","level","gamedata","gongfa","chongwu","liandan","school","place"
    ];
    foreach($dirs as $dir){
        $f=$dir."/".$class . '.php';
        if(file_exists($f)){
            require_once $f;
        }
    }
 
	
    
}); 
 
set_time_limit(0);
session_write_close();
require_once 'ai.php';
$history=help::history(10);
 
$prompt=trim($_POST['prompt']);
//清空历史数据
if($prompt=="clear"){
    gamedata::clear();
    echo json_encode(['data'=>['content'=>'历史记录已清空']]);

    exit;
}
//用户游戏数据
$gamedata=gamedata::get();
$gamedata[]=[
    "role"=>"user",
    "content"=>$prompt
];
//解析 ai生成剧情指令
if(substr($prompt,0,1)=="/"){
    $history=help::history(10);
    $player=player::get();
    $prompt.=",生成的内容不要出现选择项。还需结合玩家现在的修为等级以及所在的地图区域。玩家修为：{$player["level"]}，所在地图：{$player["map"]}";
    $msg = AIRun($prompt,$history,$system);
    if(empty($msg)){
        $msg="AI失联了";
    }
    $gamedata[]=[
        "role"=>"assistant",
        "content"=>$msg
    ];
    gamedata::set($gamedata);
    echo json_encode(['data'=>['content'=>$msg,"log"=>"ai自主"]]);
    exit;
}
//解析内置命令
$cmd=command::getbyname($prompt);
if(empty($cmd)){
    $a1=explode(" ",$prompt);
    $c1=$a1[0];

    $cmd=command::getbyname($c1);
    if(empty($cmd)){
        //用ai解析命令
        $cmd=command::getByAi($prompt);
    }   
    
}

if(!empty($cmd)){
    $con=command::run($cmd,$prompt);
    if(empty($con)){
        $con="执行命令出错";
    }
    echo json_encode(['data'=>['content'=>$con]]);
    $gamedata[]=[
        "role"=>"assistant",
        "content"=>$con
    ];
    gamedata::set($gamedata);
    exit;
}


//用AI生成剧情

//$history=json_decode($_POST['history'],true);
$history=help::history(10);
 
$history=[];

$msg = AIRun($prompt,$history,$system);
if(empty($msg)){
    $msg="AI失联了";
}
$gamedata[]=[
    "role"=>"assistant",
    "content"=>$msg
];
gamedata::set($gamedata);
echo json_encode(['data'=>['content'=>$msg,"log"=>"ai自主","history"=>$history]]); 
?>
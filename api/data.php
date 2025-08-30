<?php
define("ROOT_PATH",__DIR__);
require __DIR__."/ai.php";
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
        "command","maps","wupin","beibao","fun","npc","level","gamedata","gongfa","chongwu","liandan","school","place","skill"
    ];
    foreach($dirs as $dir){
        $f=$dir."/".$class . '.php';
        if(file_exists($f)){
            require_once $f;
        }
    }
 
	
    
}); 
$action=$_REQUEST["action"];
switch($action){
    case "aiwork":
        $cmds=[
            "修炼",
            "探索",
            "战斗",
            "采摘",
             
        ];
        $file=ROOT_PATH."/data/aiwork/".GAME_USERID.".txt";
        if(file_exists($file)){
            $log=file_get_contents($file);
        }else{
            $log="";
        }
        $prompt="现在需要你寻找最合适执行的命令，有些命令是有时间限制的，修炼要间隔180秒，战斗或者击杀要间隔180秒，采摘要间隔60秒。";
        if(!empty($log)){
            $prompt.="玩家最近执行过已下命令：".$log;
        }    
        $prompt.="现在请你给出以下最适合的命令，只要给我命令就行：".implode(",",$cmds);
        $cmd=AIAsk($prompt);
        if(empty($cmd)){
            $cmd="";
        }else{
            $log.=date("Y-m-d H:i:s") ."执行命令：".$cmd."\n";
            file_put_contents($file,$log);
        }
       
        echo json_encode([
            "prompt"=>$cmd
        ]);
        break;
    case "beibao":
            $list=beibao::getAll();
            if(!empty($list)){
                foreach($list as $k=>$v){
                    $v["ulevel"]=0;
                    $v["type"]="good";
                    //查看是否功法
                    $gf=gongfa::get($v["title"]);
                    if(!empty($gf)){
                        $v["type"]="gongfa";
                        $ux=gongfa::get_xiulian($v["title"]);
                        
                        if(!empty($ux)){
                            $v["ulevel"]=$ux["level"];
                        }
                        
                        
                    }
                    $list[$k]=$v;
                }
            }
            echo json_encode(["list"=>$list]);
        break;
    case "beibao_sell":
        $title=$_REQUEST["title"];
        beibao::sell($title);
        echo json_encode(["data"=>$title."出售成功"]);
        break;
    case "beibao_use":
        $title=$_REQUEST["title"];
        beibao::usegood($title);
        echo json_encode(["data"=>$title."使用成功"]);
        break;



    case "status":
            $data=player::get();
            $level=level::get($data["level"]);
            $data["level"]=$level["name"];
            $data["level_percent"]=round($data["level_percent"],2);
            echo json_encode(["data"=>$data]);
        break;
    case "maps":
            $list=maps::getAll();
            echo json_encode(["list"=>$list]);
        break;
    case "new_map":
            $name=$_REQUEST["name"];
            $level=intval($_REQUEST["level"]);
            $min_level=intval($_REQUEST["min_level"]);
            if(empty($name) ){
                echo json_encode(["data"=>"参数错误err"]);
                exit;
            }
            $res=maps::add($name,$level);
            echo json_encode(["data"=>$res]);
        break;
    case "del_map":
        $name=$_REQUEST["name"];
        if(empty($name)){
            echo json_encode(["data"=>"参数错误"]);
            exit;
        }
        $res=maps::delete($name);
        echo json_encode(["data"=>$res]);
        break;
    case "npcs":
            $list=npc::getAll();
            echo json_encode(["list"=>$list]);
        break;
    case "chongwu":
            $list=chongwu::getAll();
            echo json_encode(["list"=>$list]);
        break;

    case "gamedata":
            $list=gamedata::get();
            echo json_encode(["list"=>$list]);
        break;
    case "rank":
            $list=player::getRank();
            echo json_encode(["list"=>$list]);
        break;
    case "gongfa_xiulian":
       
            $name=$_REQUEST["name"];
            $res=gongfa::xiulian($name);
            echo json_encode(["data"=>$res]);
        break;
}
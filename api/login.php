<?php
define("ROOT_PATH",__DIR__);
spl_autoload_register(function ($class) {
	$class=strtolower($class);
    $dirs=[
        "command","maps","wupin","beibao","fun","npc","level"
    ];
    foreach($dirs as $dir){
        $f=$dir."/".$class . '.php';
        if(file_exists($f)){
            require_once $f;
        }
    }
 
	
    
}); 
$c=file_get_contents("php://input");
$res=json_decode($c,true);
$name=$res["name"];
$password=$res["password"];
$file=__DIR__."/login_user/".md5($name).md5($password).".json";
if($name=='admin' && file_exists(ROOT_PATH."/data/admin.lock") && !file_exists($file)){
    echo json_encode([
        "error"=>1,
        "message"=>"用户已存在",
        "user"=>[]
    ]);
    exit;

}
if(!file_exists($file)){
    file_put_contents(ROOT_PATH."/data/admin.lock",1);
    $user=[
        "userid"=>md5($name).md5($password),
        "name"=>$name
       
    ];
    file_put_contents($file,json_encode($user));
    //生成npc
    define("GAME_USERID",$user["userid"]);
     
    $p=player::get();
    $p["name"]=$name;
    $p["id"]=GAME_USERID;    
    player::set($p);
}

$c=file_get_contents($file);
$arr=json_decode($c,true);
 

echo json_encode(["user"=>$arr,"error"=>0,"message"=>"登录成功"]);
 
?>
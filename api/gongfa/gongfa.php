<?php
class gongfa{
    public static $attr=[
        "level"=>1,//等级
        "min_level"=>1,
        "level_percent"=>0,//进阶百分比"
        "name"=>"功法",
       
        "wuxing"=>"火", //五行属性
         
        "attack"=>100,//攻击力
        "magic_attack"=>100,//魔法攻击力
        "defense"=>100,//防御力
        "magic_defense"=>100,//魔法防御力
    

    ];
    public static function getAll(){
        $dir=ROOT_PATH."/data/gongfa/";
        $files=glob($dir."*.json");
       
        $list=[];
        foreach ($files as $file) {
            $data=json_decode(file_get_contents($file),true);
            $list[]=$data;
        }
        return $list;
         
    }
    public static function get($name){
        $file=ROOT_PATH."/data/gongfa/".md5($name).".json";
        if(!file_exists($file)){
            return [];
        }
        return json_decode(file_get_contents($file),true);
    }

    public static function remove($name){
        $file=ROOT_PATH."/data/gongfa/".md5($name).".json";
        if(file_exists($file)){
            unlink($file);
        }
    }

    public static function add($name,$level=1,$wuxing="火") { 
        $level=max(1,$level);
        $file=ROOT_PATH."/data/gongfa/".md5($name).".json";
        $gf=self::get($name);
        if(empty($gf)){
            $gf=self::$attr;
        }
        $gf["name"]=$name;
        $gf["level"]=$level;
        $multiplier = pow($level, 1.1); 
        $gf["attack"] = (int)($gf["attack"] * $multiplier);
        $gf["magic_attack"] = (int)($gf["magic_attack"] * $multiplier);
        $gf["defence"] = (int)($gf["defence"] * $multiplier);
        $gf["magic_defence"] = (int)($gf["magic_defence"] * $multiplier);
        $gf["experience"] = (int)($gf["experience"] * $multiplier);
        $gf["experience_next"] = (int)($gf["experience_next"] * $multiplier);
        $gf["level_num"] = rand(3,5);     
        $gf["wuxing"]=$wuxing; 
        file_put_contents($file,json_encode($gf));
        return $gf;

    }
    public static function get_xiulian($name){
        $name=trim($name);
        $file=ROOT_PATH."/data/gongfa_xiulian/".GAME_USERID.".json";
        if(!file_exists($file)){
            return [];
        }
        $list=json_decode(file_get_contents($file),true);
         
        $id=md5($name);
        if(empty($list[$id])){
            return [];
        }
        return $list[$id];
        
         
    }
    public static function xiulian($name){
        $name=trim($name);
        $file=dirname(__DIR__)."/data/gongfa_xiulian_last.log";
        if(!file_exists($file)){
            file_put_contents($file,json_encode([]));
        }
        $last=file_get_contents($file);
        $ts=json_decode($last,true);
        $now=time();
        $next_time=180;//时间间隔
        if(!empty($ts[GAME_USERID]) && $now-$ts[GAME_USERID]<$next_time){
            return "你刚刚已经修炼过了，休息一下吧！再过".($next_time+$ts[GAME_USERID]-$now)."秒可以再次修炼";
        }
        $ts[GAME_USERID]=$now;
        file_put_contents($file,json_encode($ts));
        $file=ROOT_PATH."/data/gongfa_xiulian/".GAME_USERID.".json";
        $list=[];
        if(file_exists($file)){
            $list=json_decode(file_get_contents($file),true);
        }
        $id=md5($name);
        if(!isset($list[$id])){
            $list[$id]=[
                "id"=>$id,
                "name"=>$name,
                "level"=>0,
                "level_percent"=>0
            ];
        }
        $per=rand(0,5);
        if($per==0){
            return "修炼失败";
        }
        $item=$list[$id];
         
        $item["level_percent"]+=$per;
        $con="";
        if($item["level_percent"]>=100){
            $item["level"]++;           
            $item["level_percent"]=100;
            $con="恭喜你，功法升级到大圆满。";
        }
        $con.="修炼成功,提升".$per."%,功法修炼进度：".$item["level_percent"]."%";
        $list[$id]=$item;
        file_put_contents($file,json_encode($list));
        return $con;
    }

}
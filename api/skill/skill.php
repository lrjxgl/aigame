<?php
class skill{
    public static $attr=[
        "id"=>"",
        "name"=>"",
        "level"=>0,
        "attack"=>0,
        "magic_attack"=>0,
        "defense"=>0,
        "magic_defense"=>0,
        "hp"=>0,
        "mp"=>0,

    ];
    public static function getAll(){
        $file=ROOT_PATH."/data/skill/data.json";
        if(!file_exists($file)){
            file_put_contents($file,"{}");
        }
        return json_decode(file_get_contents($file),true);

    }

    public static function add($skill){
        $skills=self::getAll();
        $key=md5($skill["name"]);
        if(!isset($skills[$key])){
            $skills[$key]=$skill;;
        }
        $skills[$key]=$skill;
        file_put_contents(ROOT_PATH."/data/skill/data.json",json_encode($skills));
    }

    public static function getByName($name){
        $skills=self::getAll();
        foreach($skills as $v){
            if($v["name"]==$name){
                return $v;
            }
        }
        return [];
    }
    
    public static function my(){
        $list=[];
        $file=ROOT_PATH."/data/skill_xiulian/".GAME_USERID.".json";
        if(!file_exists($file)){
            $list= [];
        }else{
            $list=json_decode(file_get_contents($file),true);
        }
        if(!empty($list)){
            foreach($list as $k=>$v){
                $kk=self::getByName($v["name"]);
                $kk["level_percent"]=$v["level_percent"];
                //保留一位小数点

                $kk["attack"]=round( $kk["attack"]*$kk["level_percent"]/100,1);
                $kk["magic_attack"]=intval( $kk["magic_attack"]*$kk["level_percent"]/100);
                $kk["defense"]=intval($kk["defense"]*$kk["level_percent"]/100);
                $kk["magic_defense"]= intval($kk["magic_defense"]*$kk["level_percent"]/100);
               

                $list[$k]=$kk;

            }
        }
        return $list;
    }

    public static function get_xiulian($name){
        $name=trim($name);
        $file=ROOT_PATH."/data/skill_xiulian/".GAME_USERID.".json";
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
        $file=dirname(__DIR__)."/data/skill_xiulian_last.log";
        if(!file_exists($file)){
            file_put_contents($file,json_encode([]));
        }
        $last=file_get_contents($file);
        $ts=json_decode($last,true);
        $now=time();
        $next_time=10;//时间间隔
        if(!empty($ts[GAME_USERID]) && $now-$ts[GAME_USERID]<$next_time){
            return "你刚刚已经修炼过技能了，休息一下吧！再过".($next_time+$ts[GAME_USERID]-$now)."秒可以再次修炼";
        }
        $ts[GAME_USERID]=$now;
        file_put_contents($file,json_encode($ts));
        $file=ROOT_PATH."/data/skill_xiulian/".GAME_USERID.".json";
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
                     
            $item["level_percent"]=100;
            $con="恭喜你，功法升级到大圆满。";
        }
        $con.="修炼成功,提升".$per."%,修炼进度：".$item["level_percent"]."%";
        $list[$id]=$item;
        file_put_contents($file,json_encode($list));
        return $con;
    }

    public static function getInFight(){
        $list=self::my();
        $list2=array_slice($list,0,6);
        return $list2;
    }



}

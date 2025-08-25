<?php
class school{
    public static $attr=[
        "id"=>"",
        "name"=>"",
        "userid"=>"",
        "level"=>1,//等级
        "min_level"=>0,//最低等级
        "description"=>"",
        "content"=>"",
        "x"=>0,
        "y"=>0, 
    ];
    public static function getAll(){
        $dir=ROOT_PATH."/data/school/base/";
        $files=glob($dir."*.json");
        $list=[];
        foreach($files as $file){
            $json=json_decode(file_get_contents($file),true);
            $list[]=$json;
        }
        return $list;
    }
    public static function get($name){
        $id=md5($name);
        $file=ROOT_PATH."/data/school/base/".$id.".json";
        if(!file_exists($file)){
            return [];
        }
        $attr=json_decode(file_get_contents($file),true);
        return $attr;
    }

    public static function get_school_people($name){
        $id=md5($name);
        $file=ROOT_PATH."/data/school/people/".$id.".json";
        if(!file_exists($file)){
            $peoples=[];
        }else{
            $peoples=json_decode(file_get_contents($file),true);
        }
        return $peoples;
    }

    public static function set_school_people($name,$peoples=[]){
        $id=md5($name);
        $file=ROOT_PATH."/data/school/people/".$id.".json";
        file_put_contents($file,json_encode($peoples));
        return true;
    }
    public static function join_school($name){
        
        $zm=self::get($name);
        if(empty($zm)){
            return "宗门不存在";
        }
        $id=md5($name);
        $npc=player::get();
        $last_school=$npc["school"];
        $npc["school"]=$name;
        $peoples=self::get_school_people($name);
        $peoples[]=$npc["name"];
        self::set_school_people($name,$peoples);
        //移除原来宗门的弟子列表
        $last=self::get($last_school);
        if(!empty($last)){
            $lss=self::get_school_people($last_school);
            self::set_school_people($last_school,array_diff($lss,array($npc["name"])));
        }
        player::set($npc);
        return "加入宗门成功";
    }

    public static function create($name){
        $attr=self::$attr;
        $id=md5($name);
        $file=ROOT_PATH."/data/school/base/".$id.".json";
        if(file_exists($file)){
            return "宗门已存在";
        }
        $attr["userid"]=GAME_USERID;
        $attr["id"]=$id;
        $attr["name"]=$name;
        $attr["description"]=$name;
        $attr["content"]=$name;
        file_put_contents($file,json_encode($attr));
        $npc=player::get();
        $last_school=$npc["school"];
        //加入宗门
        $peoples=self::get_school_people($name);
        $peoples[]=$npc["name"];
        self::set_school_people($name,$peoples);
        //更新所在宗门
        $npc["school"]=$name;
        player::set($npc);
        //移除原来宗门的弟子列表
        
        $last=self::get($last_school);
        if(!empty($last)){
            $lss=self::get_school_people($last_school);
            self::set_school_people($last_school,array_diff($lss,array($npc["name"])));
        }
        return "创建成功";
    }
}
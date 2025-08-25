<?php
class maps{
    public static $attr=[
        "id"=>"",
        "name"=>"",
        "level"=>1,//等级
        "min_level"=>0,//最低等级
        "description"=>"",
        "content"=>"",
        "x"=>0,
        "y"=>0,
        "z"=>0   
    ];
    public static function getAll(){
        //生成修仙世界的地图列表
        $file=ROOT_PATH."/data/maps/json/maps.json";
        if(!file_exists($file)){
            $map=self::$attr;
            $map["id"]=md5("新手村");
            $map["name"]="新手村";
            $map["description"]="新手村是每个修炼者最初来到的地方，在这里你可以学习基本的修炼技巧和基础知识。新手村的地图相对简单，适合初学者探索和学习。";
            $map["content"]="新手村是每个修炼者最初来到的地方，在这里你可以学习基本的修炼技巧和基础知识。新手村的地图相对简单，适合初学者探索和学习。";
            $list=[
                "new"=>$map
            ];
            file_put_contents($file,json_encode($list));
        }
        $list=json_decode(file_get_contents($file),true);       
       return $list;
    }

    public static function get($id){
        $list=self::getAll();
        foreach ($list as $v) {
            if($v["id"]===$id){
                $v["content"]=file_get_contents(ROOT_PATH."/data/maps/json/".trim($v["id"]).".json");
                return $v; 
            } 
            
        }
        return $list["new"];
    }

    public static function getByName($name){
        $list=self::getAll();
        foreach ($list as $v) {
            if($v["name"]===$name){
                $v["content"]=file_get_contents(ROOT_PATH."/data/maps/json/".trim($v["id"]).".json");
                return $v; 
            } 
            
        }
        return $list["new"];
    }

    public static  function delete($name){
        $list=self::getAll();
        foreach ($list as $k=>$v) {
            if($v["name"]==$name){
                $file=ROOT_PATH."/data/maps/json/".trim($v["id"]).".json";
                if(file_exists($file)){

                    @unlink($file);                    
                }

                unset($list[$k]);
            } 
            
        }
        $file=ROOT_PATH."/data/maps/json/maps.json";
        file_put_contents($file,json_encode($list));
        
        return "删除成功";
    }

    public static function add($name,$level,$min_level=0){
        $list=self::getAll();
        $id=md5($name);
        if(!empty($list[$id])){
            return "创建失败，名称已存在";
        }
        $map=self::$attr;
        $map["id"]=$id;
        $map["name"]=$name;
        $map["level"]=$level;

        $map["min_level"]=$min_level;

        $p1=$name."是一个修仙世界的地图或者修仙宗门，该地图的等级为".$level."级。请给我简单描述一下该地图的背景和特点。"; 
        $desc=AIRun($p1);
        if(empty($desc)){
            $desc=$name;
        }
        $map["description"]=$desc;
        $p1=$name."是一个修仙世界的地图或者修仙宗门，该地图的等级为".$level."级。请给我详细描述一下该地图的背景和特点。"; 
        $content=AIRun($p1);
        if(empty($content)){
            $content=$name;
        }
        file_put_contents($file=ROOT_PATH."/data/maps/json/".trim($map["id"]).".json",$content);
        $list[$id]=$map;  
        file_put_contents(ROOT_PATH."/data/maps/json/maps.json",json_encode($list));
        return "创建成功";
    }

    public static function tansuo(){
        $player=player::get();
        $v=self::getByName($player["map"]);
       
        $con=file_get_contents(ROOT_PATH."/data/maps/json/".trim($v["id"]).".json");
        $ufile=ROOT_PATH."/data/maps/logs/".GAME_USERID.md5($v["id"]).".json";
        if(!file_exists($ufile)){
            file_put_contents($ufile,json_encode([]));
        }
        $mlogs=json_decode(file_get_contents($ufile),true);
        $history=[
            [
                "role"=>"system",
                "content"=>GAME_SYSTEM."\n".$con
            ]
        ];
        if(!empty($mlogs)){
            foreach ($mlogs as $v) {
                $history[]=[
                    "role"=>"user",
                    "content"=>"探查周边区域"
                ];
                $history[]=[
                    "role"=>"assistant",
                    "content"=>$v["content"]
                ];

            } 
        }

        $history=help::history(20);
        
        $content=AIRun("请你探索当前地图{$player["map"]}下的地点{$player["place"]}的周边的场景，但尽量不要出现已探索的区域，内容字数少于300字。".$prompt,$history);
        if(empty($content)){
            $content="探索失败，没有发现任何场景";
        }else{

        }
        return $content;
    }

}

?>
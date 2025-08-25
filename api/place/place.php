<?php
class place{

    public static $attr=[
        "id"=>"",
        "name"=>"",
        "level"=>0,
        "min_level"=>0,
        "max_level"=>0,
        "description"=>"",
        "content"=>""
    ];
    /*
    *获取当前地图下的所有地方
    */
    public static function getAll($map){
        $id=md5($map);
        $file=ROOT_PATH."/data/place/base/{$id}.json";
        if(!file_exists($file)){
            file_put_contents($file,json_encode([]));
        }
        $list=json_decode(file_get_contents($file),true);
        return $list;

    }

    public static function get($map,$name){
 
        $list=self::getAll($map);
        foreach ($list as $item){
            if($item["name"]==$name){
                return $item;
            }
        }
        return [];
    }

    public static function create($map,$name,$level=0,$min_level=0,$max_level=0){
        $id=md5($map);
        $file=ROOT_PATH."/data/place/base/{$id}.json";
        if(file_exists($file)){
            $list=json_decode(file_get_contents($file),true);
        }else{
            $list=[];
        }
        $h=self::get($map,$name);
        if(!empty($h)){
            return "地点已存在";
        }
        $place=self::$attr;
        $place["name"]=$name;
        $place["id"]=md5($map.$name);
        $place["map"]=$map;
        $place["level"]=$level;
        $place["min_level"]=$min_level;
        $place["max_level"]=$max_level;
        //
        $p1="帮我生成关于地点‘{$name}’的描述，要求字数在100字左右。该地点所在地图为{$map},地点等级为{$level}，最小进入等级为{$min_level}，最大进入等级为{$max_level}。";
        $desciption=AIRun($p1);
        $place["description"]=$desciption;
        $p1="帮我生成关于地点‘{$name}’的描述，要求字数在500字左右。该地点所在地图为{$map},地点等级为{$level}，最小进入等级为{$min_level}，最大进入等级为{$max_level}。";
        $content=AIRun($p1);
        $place["content"]=$content;
        $list[]=$place;
        file_put_contents($file,json_encode($list));
        return "地点创建成功";
    }


}
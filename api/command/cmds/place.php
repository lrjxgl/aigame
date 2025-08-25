<?php
class cmds_place{

    public static $cmds=[
        "地点列表"=>[
            "title"=>"地点列表",
            "run"=>"cmds_place::getAll"
        ],
        "添加地点"=>[
            "title"=>"添加地点",
            "run"=>"cmds_place::create"
        ],
        "删除地点"=>[
            "title"=>"删除地点",
            "run"=>"cmds_place::del"
        ],
        "查看地点"=>[
            "title"=>"查看地点",
            "run"=>"cmds_place::get"
        ],
        "前往"=>[
            "title"=>"前往",
            "run"=>"cmds_place::goplace"
        ],
        
        


    ];

    public static function goplace($prompt){
        $name=trim(str_replace("前往","",$prompt));
        $player=player::get();
        $place=place::get($player["map"],$name);
        if(empty($place)){
            return "没有这个地点";
        }
        if($place["min_level"]>$player["level"]){
            return "你等级不够无法进入此地";
        }
        if($place["max_level"]<$player["level"]){
            return "你等级过高无法进入此地";
        }
        $player["place"]=$name;
        player::set($player);
        return "进入成功";

    }
    public static function getAll($prompt){
        $player=player::get();
        $list=place::getAll($player["map"]);
        $con="地点列表：\n";
        foreach($list as $item){
            $con.=$item["name"]." 等级：".$item["level"]." 最低等级：".$item["min_level"]." 最高等级：".$item["max_level"]."\n";
        }
        return $con;
    }
    public static function create($prompt){
        $p="根据下面的提示词生成json格式数据：\n".'{"name":"地点名称","level":"地点等级","min_level":"最低等级","max_level":"最高等级"}'."\n".$prompt;
        $res=AIRun($p);
        $arr=help::parseJson($res);
        if(empty($arr)){
            return "数据格式出错".$res;
        }
        $player=player::get();
        
        if(empty($arr["name"])){
            return "地点名称不能为空";
        }
        if(!isset($arr["level"])){
            return "地点等级不能为空";
        }
        if(!isset($arr["min_level"])){
            return "地点最小等级不能为空";
        }

        $res=place::create($player["map"],$arr["name"],$arr["level"],$arr["min_level"],$arr["max_level"]);
        return $res;
    }

    public static function get($prompt){
        $name=trim(str_replace("查看地点","",$prompt));
        $player=player::get();
        $place=place::get($player["map"],$name);
        if(empty($place)){
            return "没有这个地点";
        }
        $con="地点名称：".$place["name"]." 等级：".$place["level"]." 最低等级：".$place["min_level"]." 最高等级：".$place["max_level"]."\n";
         
        $con.="地点内容：".$place["content"]."\n";
        return $con;
    }

}
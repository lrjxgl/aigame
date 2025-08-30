<?php
class cmds_skill{
    public static $cmds=[
        "技能列表"=>[
            "title"=>"技能列表",
            "run"=>"cmds_skill::getAll"
        ],
        "创建技能"=>[
            "title"=>"创建技能",
            "run"=>"cmds_skill::create"
        ],
        "查看技能"=>[
            "title"=>"查看技能",
            "run"=>"cmds_skill::get"
        ],
        "修炼技能"=>[
            "title"=>"修炼技能",
            "run"=>"cmds_skill::xiulian"
        ],
        "我的技能"=>[
            "title"=>"我的技能",
            "run"=>"cmds_skill::my"
        ],
        
        
    ];

    public static function getAll($prompt){
        $list=skill::getAll();
        $con="技能列表：\n";
        foreach($list as $k=>$v){ 
            $con.="名称：".$v["name"]." 技能等级:".$v["level"]." 攻击力：".$v["attack"]."，魔法攻击力：".$v["magic_attack"] ."\n";
        }
        return $con;
    }

    public static function create($prompt){
        $p='请从下面的提示词中提取出技能的相关属性,返回json格式：{"name":"技能名称","level":"技能等级","attack":"攻击力","magic_attack":"魔法攻击力","defense":"防御力","magic_defense":"魔法防御力","hp":"血量","mp":"蓝量"}。'."\n".$prompt;
        $res=AIRun($p);
        $arr=help::parseJson($res);
        $skill=skill::$attr;
        
        if(empty($arr["name"])){
            return "技能名称不能为空";
        }
        foreach($skill as $k=>$v){
            if(!empty($arr[$k])){
                $skill[$k]=$arr[$k];
            }
        }
        skill::add($skill);
        return "技能".$skill["name"]."创建成功";
    }

    public static function get($prompt){
        $name=trim(str_replace("技能详情","",$prompt));
        $skill=skill::getbyname($name);
        if(empty($skill)){
            return "技能不存在";
        }
        $con="名称：".$skill["name"]." 技能熟练度:".$skill["level_percent"]."% 攻击力：".$skill["attack"]."，魔法攻击力：".$skill["magic_attack"] ."\n";
        return $con;
    }

    public static function xiulian($prompt){
        $name=trim(str_replace("修炼技能","",$prompt));
        return skill::xiulian($name);
    }

    public static function my($prompt){
        $list=skill::my($prompt);
        if(empty($list)){
            return "你还没有学习技能。";
        }
        $con="技能列表：\n";
        foreach($list as $k=>$v){ 
            $con.="名称：".$v["name"]." 技能熟练度:".$v["level_percent"]."% 攻击力：".$v["attack"]."，魔法攻击力：".$v["magic_attack"]." " ."\n";
            $con.="防御力：".$v["defense"]."，魔法防御力：".$v["magic_defense"] ."\n";

        }
        return $con;
    }




















}
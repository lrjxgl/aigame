<?php
class cmds_liandan{

    public static $cmds=[
        "丹方列表"=>[
            "title"=>"丹方列表",
            "run"=>"cmds_liandan::getAll"
        ],
        "创建丹方"=>[
            "title"=>"创建丹方",
            "run"=>"cmds_liandan::create"
        ],
        "查看丹方"=>[
            "title"=>"查看丹方",
            "run"=>"cmds_liandan::getOne"

        ],
        "炼丹"=>[
            "title"=>"炼丹",
            "run"=>"cmds_liandan::lian_dan"
        ],
        "使用丹药"=>[
            "title"=>"使用丹药",
            "run"=>"cmds_liandan::use_dan"
        ],


    ];

    public static function getAll(){
        $list=liandan::getAll();
        $con="丹方列表,共".count($list)."个丹方：\n";
        foreach($list as $v){
            $con.="".$v["name"]."：".$v["level"]."级\n"; 
        }
        return $con;
    }

    public static function create($prompt){
        $res=liandan::add($prompt);
        if(empty($res)){
            $res="创建丹方失败";
        }
        return $res;
    }

    public static function getOne($prompt){
        $name=trim(str_replace("查看丹方","",$prompt));
        $res=liandan::getByname($name);
        if(empty($res)){
            return "没有发现该丹方";
        }
        $con="丹方属性：\n";
        $con.="名称：".$res["name"]."\n";
        $con.="等级：".$res["level"]."\n";
        $con.="材料：\n";
        foreach($res["goods"] as $v){
            $con.=$v["name"]." x ".$v["num"].",";
        }
        $con.="\n";
        $con.="功能：".$res["description"]."\n";
        
        return $con;
    }

    public static function lian_dan($prompt){
        $name=trim(str_replace("炼丹 ","",$prompt));
        $res=liandan::lian_dan($name);
        if(empty($res)){
            return "炼丹失败";
        }
        return $res;
    }
    public static function use_dan($prompt){
        $name=trim(str_replace("使用丹药 ","",$prompt));
        $res=liandan::use_dan($name);
        if(empty($res)){
            return "使用失败";
        }
        return $res;
    }

































}
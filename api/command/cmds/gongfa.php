<?php
class cmds_gongfa{
    public static $cmds=[
        "功法列表"=>[
            "title"=>"功法列表",
            "run"=>"cmds_gongfa::getAll"
        ],
        "创建功法"=>[
            "title"=>"创建功法",
            "run"=>"cmds_gongfa::create"
        ],
        "查看功法"=>[
            "title"=>"查看功法",
            "run"=>"cmds_gongfa::get"
        ],
        "删除功法"=>[
            "title"=>"删除功法",
            "run"=>"cmds_gongfa::delete"
        ],

    ];

    public static function getAll($prompt){
        $list=gongfa::getAll();
        $con="功法列表：\n";
        foreach($list as $k=>$v){ 
            $con.="名称：".$v["name"]." 等级:".$v["level"]." 属性：".$v["wuxing"] ."\n";
        }
        return $con;
    }

    public static function create($prompt){
        $p='请从下面的提示词中提取出功法名称以及等级和五行属性（金木水火土）,如果没有则返回无，如果有则返回json格式：{"name":"功法名称","level":"功法等级","wuxing":"(金/木/水/火/土)"}。'."\n".$prompt;
        $res=AIRun($p);
        if($res=='无'){
            return "功法添加失败";
        }
        $arr=help::parseJson($res);
        if(empty($arr)){
            return "功法添加失败";
        }
            
        $name=$arr["name"];
        $level=$arr["level"];
        $wuxing="";
        if(!empty($arr["wuxing"])){
            $wuxing=$arr["wuxing"];
        }

        $res=gongfa::add($name,$level,$wuxing);
        $con="添加功法成功，功法属性：\n";
        foreach($res as $k=>$v){
            $con.="".$k."：".$v."\n"; 
        }
        return $con."\n";
    }

    public static function get($prompt){
        $name=trim(str_replace("删除功法","",$prompt));
        gongfa::remove($name);
        return "删除功法{$name}成功";
    }

    public static function delete($prompt){
        $p="请从下面的提示词中提取出要删除的功法名称,如果没有则返回无：".$prompt;
        $res=AIRun($p);
        if($res=='无'){
            return "没有发现任何需要删除的功法";
        }
        gongfa::delete($res);
        return "删除成功";
    }


}
<?php
class cmds_school{
    public static $cmds=[
        "宗门测试"=>[
            "title"=>"宗门测试",
            "run"=>"cmds_school::test",

        ],
        "宗门列表"=>[
            "title"=>"宗门列表",
            "run"=>"cmds_school::getAll"
        ],
        "创建宗门"=>[
            "title"=>"创建宗门",
            "run"=>"cmds_school::create"
        ],
        "加入宗门"=>[
            "title"=>"加入宗门",
            "run"=>"cmds_school::join"
        ],
    
    ];

    public static function getAll($prompt){
        $list=school::getAll();
        $con="宗门列表,共".count($list)."个宗门：\n";
        foreach($list as $k=>$v){
            $con.="".$v["name"]."：".$v["description"]."\n";
        }
        return $con;
    }
    public static function create($prompt){
        $name=trim(str_replace("创建宗门 ","",$prompt));
        if(empty($name)){
            return "请输入宗门名称";
        }
        $res=school::create($name);
        return $res;
    }

    public static function join($prompt){
        $name=trim(str_replace("加入宗门 ","",$prompt));
        if(empty($name)){
            return "请输入宗门名称";
        }
        $res=school::join_school($name);
        return $res;
    }
    public static function test($prompt){
        return "测试2".$prompt;
    }

}

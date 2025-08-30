<?php
class cmds_wupin{
    public static $cmds=[
        "物品列表"=>[
            "title"=>"物品列表",
            "run"=>"cmds_wupin::getAll"
        ],
        "物品详情"=>[
            "title"=>"物品详情",
            "run"=>"cmds_wupin::get"
        ],
        "使用物品"=>[
            "title"=>"使用物品",
        ],
        "创建物品"=>[
            "title"=>"创建物品",
            "run"=>"cmds_wupin::create"
        ],

    ];

    public static function getAll($prompt){
        $list=wupin::getAll();
        $con="物品列表,共".count($list)."个物品：\n";
        foreach($list as $k=>$v){
            $con.="".$v["name"]."：".$v["description"]."\n";
        }
        return $con;
    }

    public static function get($prompt){
        $name=trim(str_replace("物品详情 ","",$prompt));
        if(empty($name)){
            return "请输入物品名称";
        }
        $wupin=wupin::getByName($name);
        if(empty($wupin)){
            return "没有找到物品";
        }
        $con="物品名称：".$wupin["name"]."\n物品描述：".$wupin["description"]."\n物品类型：".$wupin["type"]."\n物品等级：".$wupin["level"]."\n物品经验值：".$wupin["exp"]."\n物品灵石：".$wupin["spirit_stones"]."\n物品血量：".$wupin["hp"]."\n物品蓝量：".$wupin["mp"];
        return $con;
    }

    public static function create($prompt){
        $good=wupin::$attr;
        $p='请从下面的内容中提取物品的属性，返回格式为：{"name":"名称","type":"类型","description":"描述","level":"等级","exp":"经验值","spirit_stones":"灵石","hp":"血量","mp":"蓝量","attack":"攻击力","magic_attack"=>"魔法攻击力","defence":"防御力","magic_defence"=>"魔法防御力"}'."\n";
        $p.=$prompt;
        $res=AIRun($p,[]);
        $arr=help::parseJson($res);
        foreach($good as $k=>$v){
            if(!empty($arr[$k])){
                $good[$k]=$arr[$k];
            }
        }
        if(empty($good["name"])){
            return "物品名称不能为空";
        }
        if(wupin::getByName($good["name"])){
            return "该物品已存在，请重新输入其他名字";
        }
        $res=wupin::add($good);
        return "物品".$good["name"]."创建成功";
    }

}
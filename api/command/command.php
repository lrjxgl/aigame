<?php
class command{

    public static function getAll(){
        
        $command_list= [
            "/"=>[
                "title"=>"/",
                "description"=>"AI互动"
            ],
            "地图列表"=>[
                "title"=>"地图列表",
        
            ],
            "帮助"=>[
                "title"=>"帮助",
                "description"=>"查看所有命令",

         
            ],
            "状态"=>[
                "title"=>"状态",
            ],
            "探索"=>[
                "title"=>"探索",

            ],
            "采摘"=>[
                "title"=>"采摘",
            ],
            "捕捉"=>["title"=>"捕捉"],
            "收养"=>["title"=>"收养"],
            "查看宠物"=>["title"=>"查看宠物"],
            "查看背包"=>["title"=>"查看背包"],
            "修炼"=>["title"=>"修炼"],
            "战斗"=>["title"=>"战斗"],
            "击杀"=>["title"=>"击杀"],
            "购买"=>["title"=>"购买"],
            "出售"=>["title"=>"出售"],
            "new_npc"=>["title"=>"new_npc"],
            "levels"=>["title"=>"levels"],
            "商店功法"=>["title"=>"商店功法"],
             
             

            "clear"=>["title"=>"clear","description"=>"清空列表"], 
            "前往地图"=>["title"=>"前往地图"],
            
        ];
        require_once __DIR__."/cmds/school.php";
      
        foreach(cmds_school::$cmds as $k=>$v){
            $command_list[$k]=$v;
        }
        require_once __DIR__."/cmds/liandan.php";
        foreach(cmds_liandan::$cmds as $k=>$v){
            $command_list[$k]=$v;
        }
        require_once __DIR__."/cmds/gongfa.php";
        foreach(cmds_gongfa::$cmds as $k=>$v){
            $command_list[$k]=$v;
        }
        require_once __DIR__."/cmds/place.php";
        foreach(cmds_place::$cmds as $k=>$v){
            $command_list[$k]=$v;
        }
        return $command_list;
    }
    
    public static function help(){
        $list=self::getAll();
        $con="命令列表：\n";
        foreach($list as $k=>$v){ 
            
            $con.="".$v["title"];
            if(!empty($v["description"])){
                $con.="：".$v["description"];
            }
            $con.="\n";
            
        }

       return $con;
    }
    public static function get($id){
        $list=self::getAll();
        if(!empty($list[$id])){
            return $list[$id];
        }

        return [];
    }

    public static function getByName($name){
        $list=self::getAll();
        foreach ($list as $k=>$v) {
            if($v["title"]==$name) return $k;
        }
        return "";
    }

    public static function getByAi($prompt){
        $list=self::getAll();
        $p="现在游戏有以下指令：\n";
        foreach ($list as $v) {
            $p.=$v["title"]."\n";
        }
        $p.="请你将下面的内容转换为上面出现的指令,如果没有则返回无，如果有则返回指令名称：".$prompt;
        $res=AIRun($p);
       
        if($res=='无'){
            return '';
        }
        return $res;

    }

    public static function run($k,$prompt=''){
         
        $history=json_decode($_POST['history'],true);
        switch($k){
            case "地图列表":
                $list=maps::getAll();
                $con="地图列表：\n";
                foreach($list as $v){
                    $con.="".$v["name"]."：".$v["desc"]."\n";

                }
                return $con;
                break;
            case "帮助":
                return self::help();
                break;
            case "探索":
                
                $c=maps::tansuo();
                return $c;
                break;
            case "前往地图":

                $p="请根据下面的提示词中提取出现的地图名称,如果有则返回第一个地图名称，如果没有则返回无：".$prompt;              
                $res=AIRun($p);
                if($res=='无'){
                    return "没有发现任何地图";
                }
                $map=maps::getByName($res);
                if(empty($map)){
                    return "没有发现任何地图";
                }
                 
                $player=player::get();
                if($player["level"]<$map["min_level"]){
                    return "您的等级不足，无法前往该地图";
                }
                $player["map"]=$map["name"];
                player::set($player);
                return "前往".$map["name"]."成功";
                break;
            case "采摘":
            case "捕捉":
                $name=trim(str_replace($k,"",$prompt));
                if(empty($name)){
                    return "没有发现任何物品";
                }
                if(!help::history_has_word($name)){
                    return "没有发现任何物品";
                }
                $c=beibao::add($name,"1");
                return $name."放入背包";
                /*
                $p="请根据下面的提示词中提取出物品或者生物名称,如果有则返回第一个物品或生物名称，如果没有则返回无：".$prompt;
                
                $res=AIRun($p);
                if($res=='无'){
                    return "没有发现任何物品";
                }
                $c=beibao::add($res,"1");
                return $res."放入背包";
                */
                break;
            case "收养":
                $p="请从下面的提示词中提取出生物名称,如果有则返回第一个生物名称，如果没有则返回无：".$prompt;
                
                $res=AIRun($p);
                if($res=='无'){
                    return "没有发现任何物品";
                }
                if(!help::history_has_word($res)){
                    return "没有发现任何物品";
                }
                $c=chongwu::add($res,"1");
                return $res."放入宠物栏";
                break;
            case "查看宠物":
                $p="请从下面的提示词中提取出宠物名称,如果有则返回第一个宠物名称，如果没有则返回无：".$prompt;
                
                $res=AIRun($p);
                if($res=='无'){
                    return "没有发现任何宠物".$prompt;
                }
                 
                $c=chongwu::getByName($res);
                if(empty($c)){
                    return "没有发现任何宠物。".$res;
                }
                $con="宠物名称：".$c["name"]."\n";
                $con.="等级：".$c["level"]."\n";
                $con.="生命值：".$c["hp"]."\n";
                $con.="攻击力：".$c["attack"]."\n";
                $con.="防御力：".$c["defense"]."\n";
                return $con;

                
                break;
            case "购买":
                $p="请从下面的提示词中提取出物品或者生物名称,如果有则返回第一个物品或生物名称，如果没有则返回无：".$prompt;
                $res=AIRun($p);
                if($p=='无'){
                    return "没有发现任何物品";
                }
                if(!help::history_has_word($res)){
                    return "没有发现任何物品";
                }
                $c=beibao::add($res,"1");
                return $res."放入背包";
                break;
            case "出售":
                $p="请从下面的提示词中提取出物品或者生物名称,如果有则返回第一个物品或生物名称，如果没有则返回无：".$prompt;
                $res=AIRun($p);
                if($p=='无'){
                    return "没有发现任何物品";
                }
                $product=wupin::getByName($res);
                $c=beibao::minus($res,"1");
                //增加金币或者灵石

                return $res."出售成功";
                break;

            case "查看背包":
                $res=beibao::getAll();
                $con="";
                if(empty($res)){
                    return "背包空空如也";
                }
                foreach($res as $v){
                    $con.=$v["num"]."个".$v["title"]."\n";  
                }
                return $con;
                break;
            case "修炼":
                return xiulian::run();
                break;
            case "战斗":
            case "击杀":
                return fight ::run($prompt);
                break;
            case "状态":
                $playerStatus=player::get();
                $con="玩家当前状态：\n";
                /*
                foreach($playerStatus as $k=>$v){
                    $con.="".$k."：".$v."\n"; 
                }
                */
                $level=level::get($playerStatus["level"]);
                $con.="玩家名字：".$playerStatus["name"]."\n";
                $con.="等级：".$level["name"]."\n";
                $con.="宗门：".$playerStatus["school"]."\n";
                $con.="家族：".$playerStatus["family"]."\n";  
                $con.="所在地图：".$playerStatus["map"]."\n";
                $con.="当前位置：".$playerStatus["place"]."\n";
                $con.="物理攻击：".$playerStatus["attack"]."\n";     
                $con.="魔法攻击：".$playerStatus["magic_attack"]."\n";     
                $con.="物理防御：".$playerStatus["defense"]."\n";     
                $con.="魔法防御：".$playerStatus["magic_defense"]."\n";    

                $con.="金币：".$playerStatus["gold"]."\n";
                $con.="灵石：".$playerStatus["spirit_stones"]."\n";
                return $con;
                break;
            case "new_npc":
                $p='请从下面的提示词中提取出npc名称以及等级,如果没有则返回无，如果有则返回json格式：{"name":"npc名称","level":"npc等级"}。'."\n".$prompt;
                $res=AIRun($p);
                if($res=='无'){
                    return "npc添加失败";
                }
                $arr=help::parseJson($res);
                if(empty($arr)){
                    return "npc添加失败";
                }
                $name=$arr["name"];
                $level=$arr["level"];
                $res=npc::add($name,$level);
                $con="添加npc成功，npc属性：\n";
                foreach($res as $k=>$v){
                    $con.="".$k."：".$v."\n"; 
                }
                return $con;
                break;
           
                
                break;
            
             
            

            case "levels":
                $list=level::getAll();
                $con="等级列表,共".count($list)."个等级：\n";
                foreach($list as $v){
                    $con.="".$v["name"]."：".$v["description"]."经验值:".$v["experience"]."\n"; 
                }
                return $con;
                break;
            case "商店功法":
                $level=level::get(player::get()["level"]);
                $p="请生成适合".$level["name"]."的功法名称，如果没有则返回无：".$prompt;
                $res=AIRun($p,[]);
                if($res=="无"){
                    return "商店无功法出售";
                }
                return $res;
            case "clear":
                gamedata::clear();
                return "消息已清空";
           
            
            

            

            
             
            

            default:
                $cmd=self::get($k); 
                if(is_callable($cmd["run"])){
                    $res= call_user_func($cmd["run"],$prompt);
                    if(!empty($res)){
                        return $res;
                    }
                }
                

                return "指令运行出错";
                break;

        }
    }


}
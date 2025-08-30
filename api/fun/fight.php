<?php
class fight{
    public static function run($prompt){
        $file=dirname(__DIR__)."/data/fight_last.log";
        if(!file_exists($file)){
            file_put_contents($file,json_encode([]));
        }
        $last=file_get_contents($file);
        $ts=json_decode($last,true);
        $now=time();
        $next_time=10;//时间间隔
        if(!empty($ts[GAME_USERID]) && $now-$ts[GAME_USERID]<$next_time){
            return "你刚刚已经战斗过了，休息一下吧！再过".($next_time+$ts[GAME_USERID]-$now)."秒可以再次战斗";
        }
        $ts[GAME_USERID]=$now;
        $name="";
        if(strpos($prompt,"战斗")!==false){
            $name=trim(str_replace("战斗 ","",$prompt));
        }else if(strpos($prompt,"击杀")!==false){
            $name=trim(str_replace("击杀 ","",$prompt));
        }
        if(empty($name)){
            $p="请你从下面的内容中提取玩家要战斗的对象（敌人/npc/妖兽/动物）,如果没有战斗对象（敌人/npc/妖兽/动物）则返回无：".$prompt;
            $res=AIRun($p);
            if($res!='无'){
                $name=$res;
            }
        }
       
        if($name==''){
            $p="请你从历史对话中提取战斗的对象（敌人/npc/妖兽/动物）,需要没有对战过,如果没有战斗的对象（敌人/npc/妖兽/动物）则返回无,有只要返回敌人名字。";
            $history=help::history(10);
            
            $res=AIRun($p,$history);
             
            if($res=='无'){
                return "没有发现任何战斗对象";
            }
            $name=$res;
        }
        if(!help::history_has_word($name)){
            return "没有发现任何战斗对象";
        }
        file_put_contents($file,json_encode($ts));
        $playerStatus=player::get();
        //构建玩家当前状态
        $p="玩家当前状态：\n修为等级：".$playerStatus["level"]."。\n";
        $p.="玩家当前血量：".$playerStatus["hp"]."，魔法值：".$playerStatus["mp"]." 攻击力：".$playerStatus["attack"]."，魔法攻击力：".$playerStatus["magic_attack"]."\n";
        $p.="玩家当前防御力：".$playerStatus["defense"]."，魔法防御力：".$playerStatus["magic_defense"]."\n";
        //玩家可使用的物品
        $beibao=beibao::getInFight();
        if(empty($beibao)){
            $p.="玩家没有可使用的物品\n";
        }else{
            $p.="玩家背包可使用的物品：\n";
            foreach($beibao as $v){
                $p.=$v["name"]." x ".$v["num"]."\n";
            }
        }
         
        
     
        //玩家可使用的技能
        $skill=skill::getInFight();
        $p.="玩家可使用的技能：\n";
        $skillcon="";
        foreach($skill as $v){
            $skillcon.="名称：".$v["name"]." 技能熟练度:".$v["level_percent"]."% 攻击力：".$v["attack"]."，魔法攻击力：".$v["magic_attack"]." " ;
            $skillcon.="防御力：".$v["defense"]."，魔法防御力：".$v["magic_defense"] ."\n\n";
        }
        //玩家当前状态 结束
        //敌人状态

        $pdesc="战斗过程要精彩，惊险刺激";        
        $p.="请描述玩家和".$name."之间的战斗过程，{$pdesc},玩家的状态,使用的物品(use_goods)以及获取的物品(goods)奖励，修为提升等。获得的奖励要和玩家当前修为匹配。返回json数据，格式为：".'{"content":"战斗过程",hp:"生命值",mp:"魔法值","percent":1,"goods":[{"name":"百草金丹","num":1}],"use_goods":[{"name":"百草金丹","num":1}]}';
        $fight_system=file_get_contents(ROOT_PATH."/config/ai/fight.txt");
        $messages=[
            [
                "role"=> "system",
                "content"=>$fight_system
            ]
        ];
         
        $res1=AIRun($p,$messages,GAME_SYSTEM);
        
      
        $arr=json_decode($res1,true);
        if(empty($arr)){
            preg_match("/```json(.*)```/iUs",$res1,$a1);
            if(empty($a1[1])){
                return "战斗失败".$res1;
            }
            $res=$a1[1];
            $arr=json_decode(trim($res),true);
            if(empty($arr)){
                return "战斗失败".$res1;
            }
            
        }
        $con=$arr["content"]."\n";
         $userStatus=player::get();
        if(!empty($arr["percent"])){
            $con.="修为提升".$arr["percent"]."%。\n";
           
            
            $userStatus["level_percent"]+=$arr["percent"];
           
            if($userStatus["level_percent"]>100){
                $userStatus["level"]++;
                $userStatus["level_percent"]=0;   
                $con.="恭喜你，你已升阶！当前境界为：".$userStatus["level"]."级。\n";            
            }
            
       
            
        }
        if(isset($arr["hp"])){
            $userStatus["hp"]=$arr["hp"];
        }
        if(isset($arr["mp"])){
            $userStatus["mp"]=$arr["mp"];
        }
        player::set($userStatus);
        if(!empty($arr["goods"])){
            $con.="获得了";
            foreach ($arr["goods"] as $v) {
                $con.=$v["name"]."x".$v["num"]."，";
                beibao::add($v["name"],$v["num"]);
            }
        }

         if(!empty($arr["use_goods"])){
            $con.="获得了";
            foreach ($arr["use_goods"] as $v) {
                $con.=$v["name"]."x".$v["num"]."，";
                beibao::minus($v["name"],$v["num"]);
            }
        }
         
        return $con;
    }

}
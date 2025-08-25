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
        $next_time=120;//时间间隔
        if(!empty($ts[GAME_USERID]) && $now-$ts[GAME_USERID]<$next_time){
            return "你刚刚已经战斗过了，休息一下吧！再过".($next_time+$ts[GAME_USERID]-$now)."秒可以再次战斗";
        }
        $ts[GAME_USERID]=$now;
        
        $p="请你从下面的内容中提取玩家要战斗的对象（敌人/npc/妖兽/动物）,如果没有战斗对象（敌人/npc/妖兽/动物）则返回无：".$prompt;
        $res=AIAsk($p);
        if($res=='无'){
            $p="请你从历史对话中提取战斗的对象（敌人/npc/妖兽/动物）,需要没有对战过,如果没有战斗的对象（敌人/npc/妖兽/动物）则返回无,有只要返回敌人名字。";
            $history=help::history(10);
            
            $res=AIRun($p,$history);
             
            if($res=='无'){
                return "没有发现任何战斗对象";
            }
        }
        if(!help::history_has_word($res)){
            return "没有发现任何战斗对象";
        }
        file_put_contents($file,json_encode($ts));
        $playerStatus=player::get();
        $p.="玩家当前状态：\n修为等级：".$playerStatus["level"]."。\n";
        $pdesc="战斗过程要精彩，惊险刺激";
        $p.="请描述玩家和".$res."之间的战斗过程，{$pdesc},以及获取的物品奖励，修为提升等。获得的奖励要和玩家当前修为匹配。返回json数据，格式为：".'{"content":"战斗过程","percent":1,"goods":[{"name":"百草金丹","num":1}]}';
        
        $res1=AIRun($p,[],GAME_SYSTEM);
        
      
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
        if(!empty($arr["percent"])){
            $con.="修为提升".$arr["percent"]."%。\n";
            $userStatus=player::get();
            
            $userStatus["level_percent"]+=$arr["percent"];
           
            if($userStatus["level_percent"]>100){
                $userStatus["level"]++;
                $userStatus["level_percent"]=0;   
                $con.="恭喜你，你已升阶！当前境界为：".$userStatus["level"]."级。\n";            
            }
            player::set($userStatus);
        }
        if(!empty($arr["goods"])){
            $con.="获得了";
            foreach ($arr["goods"] as $v) {
                $con.=$v["name"]."x".$v["num"]."，";
                beibao::add($v["name"],$v["num"]);
            }
        }
         
        return $con;
    }

}
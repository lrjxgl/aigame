<?php
class beibao{
    
    public static function getAll(){
        $file=ROOT_PATH."/data/beibao/".GAME_USERID.".json";
        if(!file_exists($file)){
            file_put_contents($file,"[]");
        }
        return json_decode(file_get_contents($file),true);
    }

     
    public static function getByName($name){
        $list=self::getAll();
        foreach($list as $item){
            if($item["title"]==$name){
                return $item;
            }
        }
        return [];
    }    

    /*
    将物品放入背包
    */
    public static function add($title,$num=1){
        $title=trim($title);
        $items=self::getAll();
        //判断物品是否存在
        $type="unknown";
        $good=wupin::getByName($title);
        if(!empty($good)){
            $type=$good["type"];
        }
        $item_id=md5($title);
        if(!array_key_exists($item_id,$items)){
            $items[$item_id]=[
                "title"=>$title,
                "type"=>$type,
                "num"=>$num
            ];
        }else{
            $items[$item_id]["num"]+=$num;
        } 
        
        file_put_contents(ROOT_PATH."/data/beibao/".GAME_USERID.".json",json_encode($items));
    }
    //出售物品
    public static function sell($title){
        $items=self::getAll();
        $item_id=md5($title);
        if(!array_key_exists($item_id,$items)){
            return 0;
        } 
        //从背包移出
        //物品数量不足
        if($items[$item_id]["num"] >= 1){
            $items[$item_id]["num"]--;
        }  
        
        if($items[$item_id]["num"]==0){
            unset($items[$item_id]);
        }
        $good=wupin::getByName($title);
        $spirit_stones=1;
        $gold=1;
        if(!empty($good)){
            $spirit_stones=$good["spirit_stones"];
            $gold=$good["gold"];
        }
        
        $player=player::get();
        $player["spirit_stones"]+= $spirit_stones;
        $player["gold"]+= $gold;
        player::set($player);    
        file_put_contents(ROOT_PATH."/data/beibao/".GAME_USERID.".json",json_encode($items));
    }
    
    //使用物品
    public static function usegood($title){
        $items=self::getAll();
        $item_id=md5($title);
        if(!array_key_exists($item_id,$items)){
            return 0;
        } 
        //从背包移出
        //物品数量不足
        if($items[$item_id]["num"] >= 1){
            $items[$item_id]["num"]--;
        }     
        
        if($items[$item_id]["num"]==0){ 
            unset($items[$item_id]);
        }
        $good=wupin::getByName($title);
        $hp=0;
        $mp=0;
        $attack=0;
        $defense=0;
        $exp=0;
        if(!empty($good)){
            $hp=$good["hp"];
            $attack=$good["attack"];
            $defense=$good["defense"];
            $exp=$good["exp"];
            $mp=$good["mp"];

        }
        
        $player=player::get();
        $player["hp"]+= $hp;
        $player["mp"]+= $mp;
        $player["attack"]+= $attack;
        $ulevel=level::get($player["level"]);
        $level_percent=intval($exp*100/$ulevel["experience"]);
        $a1=npc::addlevel($player,$level_percent);
        $player=$a1["npc"];
        $player["exp"]+= $exp;

        $player["defense"]+= $defense;
        player::set($player);    
        file_put_contents(ROOT_PATH."/data/beibao/".GAME_USERID.".json",json_encode($items));
    }
    /**
     * 从背包取出物品
     */
    public static function minus($title,$num=1){
        $items=self::getAll();
        $item_id=md5($title);
        if(!array_key_exists($item_id,$items)){
            return 0;
        } 
        //从背包移出
        //物品数量不足
        if($items[$item_id]["num"] >= $num){
            $items[$item_id]["num"]-=$num;
        }  
        
        if($items[$item_id]["num"]==0){
            unset($items[$item_id]);
        }
        file_put_contents(ROOT_PATH."/data/beibao/".GAME_USERID.".json",json_encode($items));
    }
    /**
     * 根据物品类型获取物品
     */
    public static function getByType($type='health'){
        $list=self::getAll();
        $arr=[];
        foreach($list as $item){
            if($item["type"]==$type){
               $arr[]=$item;
            }
        }
        return $arr;
    }
    /**
     * 战斗中可使用的物品
     */
    public static function getInFight(){
        $list=self::getAll();
        //print_r($list);
        $arr=[];
        foreach($list as $item){
            $attr="";
            switch($item["type"]){
                case "attack":
                    $item["attr"]="攻击力:".$item["attack"]." 魔法攻击力：".$item["magic_attack"];
                    $arr["attr"]=$item;
                    break;
                case "defense":
                    $item["attr"]="防御力:".$item["defense"]." 魔法防御力：".$item["magic_defense"];
                    $arr["attr"]=$item;
                    break;
                case "health":
                    $item["attr"]="补充生命值：".$item["hp"]." 补充魔法值：".$item["mp"];
                    $arr["attr"]=$item;
                    break;
            }
           
            
        }
        return $arr;
    } 


}
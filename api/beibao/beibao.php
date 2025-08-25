<?php
class beibao{
    
    public static function getAll(){
        $file=ROOT_PATH."/data/beibao/".GAME_USERID.".json";
        if(!file_exists($file)){
            file_put_contents($file,"{}");
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
        $item_id=md5($title);
        if(!array_key_exists($item_id,$items)){
            $items[$item_id]=[
                "title"=>$title,

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
        $hp=1;
        $attack=1;
        $defense=1;
        $exp=1;
        if(!empty($good)){
            $hp=$good["hp"];
            $attack=$good["attack"];
            $defense=$good["defense"];
            $exp=$good["exp"];

        }
        
        $player=player::get();
        $player["health"]+= $hp;
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


}
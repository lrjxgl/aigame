<?php
class chongwu{
    
    public static function getAll(){
        $file=ROOT_PATH."/data/chongwu/".GAME_USERID.".json";
        if(!file_exists($file)){
            file_put_contents($file,"{}");
        }
        return json_decode(file_get_contents($file),true);
    }

    public static function getByName($name){
        $list=self::getAll();
        foreach ($list as $k=>$v) { 
            if($v["name"]==$name) return $v;
        }
        return [];
    }

    /*
    将物品放入宠物栏
    */
    public static function add($title){
        $items=self::getAll();
        //判断物品是否存在
        $item_id=md5($title);
        if(!array_key_exists($item_id,$items)){
            $npc=npc::add($title,1,false);
            $items[$item_id]=$npc;
        }else{
            $title=$title."1";
            self::add($title);
            return false;
        } 
        
        file_put_contents(ROOT_PATH."/data/chongwu/".GAME_USERID.".json",json_encode($items));
    } 
    /**
     * 从背包取出物品
     */
    public static function remove($title,$num=1){
        $items=self::getAll();
        $item_id=md5($title);
        if(!array_key_exists($item_id,$items)){
            return 0;
        } 
        //从宠物栏移出
        unset($items[$item_id]);
        file_put_contents(ROOT_PATH."/data/chongwu/".GAME_USERID.".json",json_encode($items));
    }


}
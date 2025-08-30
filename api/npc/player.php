<?php
class player{
    public static function get(){
        $playerStatus = npc::$attr;
        $file=ROOT_PATH."/data/npc/user/".GAME_USERID.".json";
        if(!file_exists($file)){
            //保存当前数据
            file_put_contents($file,json_encode($playerStatus));
        }
        $player=json_decode(file_get_contents($file),true);
        if(!isset($player["map"])){
            $player["map"]="新手村";
        }
        return $player;
    }

    public static function set($playerStatus){ 
        $file=ROOT_PATH."/data/npc/user/".GAME_USERID.".json";
        $playerStatus["hp"]=max($playerStatus["hp"],0);
        $playerStatus["mp"]=max($playerStatus["mp"],0);
        //保存当前数据
        file_put_contents($file,json_encode($playerStatus));
        return true;
    }
    public static function getAll(){
        $dir=ROOT_PATH."/data/npc/user/";
        $files=glob($dir."*.json");
       
        $npcs=[];
        foreach ($files as $file) {
            $npc=json_decode(file_get_contents($file),true);
            $npcs[]=$npc;
        }
        return $npcs;
    }
    public static function getRank(){
        $npcs=self::getAll();
        
        usort($npcs,function($a,$b){
            return $b["level"]-$a["level"];
        });
        $npcs=array_slice($npcs,0,10);
        return $npcs;
    }

}

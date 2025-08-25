<?php
class gamedata{
    public static function get(){
        $file=ROOT_PATH."/data/gamedata/".GAME_USERID.".json";
        if(!file_exists($file)){
            file_put_contents($file,json_encode([]));
        }
        $data=file_get_contents($file);
        return json_decode($data,true);
    }
    public static function set($data){
        $file=ROOT_PATH."/data/gamedata/".GAME_USERID.".json";
        file_put_contents($file,json_encode($data));
    }

    public static function clear(){
        $file=ROOT_PATH."/data/gamedata/".GAME_USERID.".bak.json";
        if(!file_exists($file)){
            file_put_contents($file,json_encode([]));
        }
        $backdata=json_decode(file_get_contents($file),true);
        $list=self::get();
        $list=array_merge($backdata,array_values($list));
        file_put_contents($file,json_encode($list));
        self::set([]);
        
    }

}

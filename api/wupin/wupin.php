<?php
class wupin{
    public static $attr=[
        "id"=>"",
        "name"=>"",
        "level"=>1,
        "exp"=>5,
        "gold"=>50,
        "spirit_stones"=>5,
        "hp"=>10,
        "attack"=>1,
        "defense"=>1,
        "description"=>"获取10点生命值",
    ]; 
    public static function getAll(){
        $file=ROOT_PATH."/data/wupin/wupin.json";
        if(!file_exists($file)){
            file_put_contents($file,"[]");
        }
        return json_decode(file_get_contents($file),true);
    }
    /**
     * $good= [
     *      "name"=>"生命之水",
     *       "level"=>1,
     *       "exp"=>5,
     *       "gold"=>50,
     *      "spirit_stones"=>5,
     *       "hp"=>10,
     *       "description"=>"获取10点生命值",
     * ]
     * 
     * ]
     */
    public static function add($good){
        $items=self::getAll();
        $items[]=$good;
        file_put_contents(ROOT_PATH."/data/wupin/wupin.json",json_encode($items));
        return $items;
    }
    public static function getByname($name){
        $list=self::getAll();
        foreach ($list as $k=>$v) {
            if($v["name"]==$name){
                return $v;
            }
        }   
        return [];
    }
}
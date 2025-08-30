<?php
class wupin{
    /**
     * type
     *  health 生命值
     *  lucky 幸运值
     *  attack 攻击力
     *  defense 防御力
     */
    public static $attr=[
        "id"=>"",
        "name"=>"",
        "level"=>0,
        "exp"=>0,
        "gold"=>50,
        "spirit_stones"=>5,
        "hp"=>0,
        "mp"=>0,
        "attack"=>0,
        "defense"=>0,
        "description"=>"获取10点生命值",
        "type"=>"health"
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
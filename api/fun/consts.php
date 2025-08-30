<?php
class consts{

    public static function get_base_name($k){
        $data=[
            "name"=>"名称",
            "level"=>"等级",
            "level_percent"=>"修为进度",
            "attack"=>"攻击力",
            "magic_attack"=>"魔法攻击力",
            "defense"=>"防御力",
            "magic_defense"=>"魔法防御力",
            "hp"=>"血量",
            "mp"=>"蓝量",
            "wuxing"=>"五行",
            "xiulian_level"=>"修为等级",

        ]; 
        if(isset($data[$k])){
            return $data[$k];
        }else{
            return $k;

        }
    }

}
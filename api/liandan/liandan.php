<?php
class liandan{
    public static $attr=[
        "name"=>"",//名称
        "level"=>1,//单方等级
        'description'=>'',//描述
        "goods"=>[],//配方列表

    ];

    public static function getAll(){
        $file=ROOT_PATH."/data/liandan/danfang.json";
        if(!file_exists($file)){
            file_put_contents($file,json_encode([]));
        }
        $list=json_decode(file_get_contents($file),true);
        return $list;           
    }

    public static function get($id){
        $list=self::getAll();
        if(!empty($list[$id])){
            return $list[$id];
        }
        return [];
    }   
  
    public static function getByName($name){
        $list=self::getAll();
        foreach($list as $item){
            if($item["name"]==$name){
                return $item;
            }
        }
        //ai获取
        $p="请从下面的提示词中提取丹方名称,如果有则返回第一个丹方名称，如果没有则返回无：".$name;
        $name=AIRun($p);
        if($name=='无'){
            return [];
        }
        foreach($list as $item){
            if($item["name"]==$name){
                return $item;
            }
        }
        return [];
    }

    public static function add($prompt){
        $list=self::getAll();
        $p="根据下面的提示词生成json格式数据：\n".'{"name":"丹方名字","level":"丹方等级","goods":[{"name":"丹方材料名称","num":"丹方材料数量"}]}'."\n".$prompt;
        $res=AIRun($p);
        $arr=json_decode($res,true);
        if(empty($arr)){
            preg_match("/```json(.*)```/iUs",$res,$a1);
            if(empty($a1[1])){
                return "生成失败".$res;
            }
            $res=$a1[1];
            $arr=json_decode(trim($res),true);
            if(empty($arr)){
                return "生成失败".$res;
            }
        }
        if(empty($arr["name"]) || empty($arr["level"]) || empty($arr["goods"])){
            return "生成失败";
        }
        $name=$arr["name"];
        $level=$arr["level"];
        $goods=$arr["goods"];
        $id=md5($name.$level);
        if(!empty($list[$id])){
            return "配方已存在";
        }
        $map=self::$attr;
        $map["id"]=$id;
        $map["name"]=$name;
        $map["level"]=$level;
        $map["goods"]=$goods;
        $p="请生成关于修仙游戏的丹药“".$name."”的功效描述";
        $description=AIRun($p);
        $map["description"]=$description;
        $list[$id]=$map;  
        file_put_contents(ROOT_PATH."/data/liandan/danfang.json",json_encode($list));
        return "创建成功";
    }
    /**
     * 炼丹
     */
    public static function lian_Dan($name){
        $dan=self::getByName($name);
        if(empty($dan)){
            return "没有发现".$name;
        }
         
        $dan_goods=$dan["goods"];
        foreach($dan_goods as $good){
            $bg=beibao::getByName($good["name"]);
            if(empty($bg) || $bg["num"]<$good["num"]){
                return "材料".$good["name"]."不足，炼丹失败";
            }
        }
        //炼制成功
        $num=rand(0,10);
        beibao::add($name,$num);
        foreach($dan_goods as $good){
            beibao::minus($good["name"],$good["num"]);
        }
        return $name."炼制成功".$num."颗";
    }

    //使用丹药
    public static function use_dan($prompt){
        $name=trim(str_replace("使用丹药","",$prompt)); 
        $dan=self::getByName($name);
        if(empty($dan)){
            return "没有发现该丹方";
        }
        return "使用成功";
    }

}
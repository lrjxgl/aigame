<?php
class help{
    
    public static function parseJson($str){
        $a1=json_decode($str,true);
        if(empty($a1)){
            preg_match("/```json(.*)```/iUs",$str,$a1);
            if(empty($a1[1])){
                return [];
            }
            $str=$a1[1];
            $a1=json_decode(trim($str),true);
            if(empty($a1)){
                return [];
            }
        }
        return $a1;
    }

    public static function history($limit=10){
        $history=json_decode($_POST['history'],true);
       
        if(empty($history)){
            return [];
        }
        foreach ($history as $k=>$v) {
            if($v["role"]!="user" && $v["role"]!="assistant"){
                unset($history[$k]);
            }
             
            if($v["role"]=="user" && $v["content"]=="修炼"){
                unset($history[$k]);
                unset($history[$k+1]);
            }
        }
        // 让history 按照 role=user role=assistant 的顺序,
        $list=[];
        $need="user";
        
        if(!empty($history)){
            foreach($history as $v){
                if($v["role"]==$need){
                    if(empty($v["content"])){
                        $v["content"]="空";
                    }
                    $list[]=$v;
                    $need=$need=="user"?"assistant":"user";
                }
            }
            if($list[count($list)-1]["role"]!="assistant"){
               unset($list[count($list)-1]);
            }
             
            $newList=[];
            $len=count($list);
            $start=max(0,$len-$limit);
            for($i=$start;$i<$len;$i++){
                $newList[]=$list[$i];
            }
        }
        

        return $newList;
    } 


    public static function history_has_word($word){
        $res=self::history(10);
        $con="";
        foreach($res as $v){
            if($v["role"]=='assistant'){
                $con.=$v["content"]."\n";
            }
        }
        //判断$con是否包含$word字符串
        if(strpos($con,$word)!==false){
            return true;
        }else{
            return false;
        }

    }


}
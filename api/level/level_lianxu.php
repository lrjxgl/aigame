<?php
class level_lianxu{ 
    public static function getAll(){ 
        $arr=[];
        for($i=1;$i<10;$i++){
            $arr[]=[
                'name'=>'练虚境'.$i."层",
                'description'=>'练虚境'.$i."层",
                'experience'=>$i*100000
            ];
        }
        return $arr;
    }
}
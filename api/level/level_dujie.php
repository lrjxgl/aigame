<?php
class level_dujie{ 
    public static function getAll(){ 
        $arr=[];
        for($i=1;$i<2;$i++){
            $arr[]=[
                'name'=>'渡劫境'.$i."层",
                'description'=>'渡劫境'.$i."层",
                'experience'=>$i*100000000
            ];
        }
        return $arr;
    }
}
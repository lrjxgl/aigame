<?php
class level_heti{ 
    public static function getAll(){ 
        $arr=[];
        for($i=1;$i<10;$i++){
            $arr[]=[
                'name'=>'合体境'.$i."层",
                'description'=>'合体境'.$i."层",
                'experience'=>$i*1000000
            ];
        }
        return $arr;
    }
}
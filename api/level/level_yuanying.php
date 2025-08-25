<?php
class level_yuanying{ 
    public static function getAll(){ 
        $arr=[];
        for($i=1;$i<10;$i++){
            $arr[]=[
                'name'=>'元婴期'.$i."层",
                'description'=>'元婴期'.$i."层",
                'experience'=>$i*1000
            ];
        }
        return $arr;
    }
}
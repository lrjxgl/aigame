<?php
class level_lianqi{
    public static function getAll(){ 
        $arr=[];
        for($i=1;$i<10;$i++){
            $arr[]=[
                'name'=>'练气期'.$i."层",
                'description'=>'练气期'.$i."层",
                'experience'=>$i*10
            ];
        }
        return $arr;
    }   
}
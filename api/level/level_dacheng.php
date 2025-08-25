<?php
class level_dacheng{ 
    public static function getAll(){ 
        $arr=[];
        for($i=1;$i<10;$i++){
            $arr[]=[
                'name'=>'大乘境'.$i."层",
                'description'=>'大乘境'.$i."层",
                'experience'=>$i*10000000
            ];
        }
        return $arr;
    }
}
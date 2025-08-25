<?php
class level_huashen{
    public static function getAll(){ 
        $arr=[];
        for($i=1;$i<10;$i++){
            $arr[]=[
                'name'=>'化神期'.$i."层",
                'description'=>'化神期'.$i."层",
                'experience'=>$i*10000
            ];
        }
        return $arr;
    }
}

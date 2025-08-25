<?php
class level_jindan{ 
    public static function getAll(){ 
        $arr=[];
        for($i=1;$i<10;$i++){
            $arr[]=[
                'name'=>'金丹期'.$i."层",
                'description'=>'金丹期'.$i."层",
                'experience'=>$i*100
            ];
        }
        return $arr;
    }
}

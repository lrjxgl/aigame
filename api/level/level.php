<?php
class level{
    public static function getAll(){ 
        $levels=[];
        $levels[0]=[
            'name'=>'凡人',
            'description'=>'凡人，还未入门修仙',
            'experience'=>0                
        ];
        $levels=array_merge($levels,array_values(level_lianqi::getAll()));
        $levels=array_merge($levels,array_values(level_jindan::getAll()));
        $levels=array_merge($levels,array_values(level_yuanying::getAll()));
        $levels=array_merge($levels,array_values(level_huashen::getAll())); 
        $levels=array_merge($levels,array_values(level_lianxu::getAll()));
        $levels=array_merge($levels,array_values(level_heti::getAll()));
       
        $levels=array_merge($levels,array_values(level_dacheng::getAll()));
        $levels=array_merge($levels,array_values(level_dujie::getAll()));

        return $levels;
    }

    public static function get($level){
        return self::getAll()[$level];
    }
}
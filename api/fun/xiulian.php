<?php
class xiulian{
  
    public static function run(){
        $file=dirname(__DIR__)."/data/xiulian_last.log";
        if(!file_exists($file)){
            file_put_contents($file,json_encode([]));
        }
        $last=file_get_contents($file);
        $ts=json_decode($last,true);
        $now=time();
        $next_time=180;//时间间隔
        if(!empty($ts[GAME_USERID]) && $now-$ts[GAME_USERID]<$next_time){
            return "你刚刚已经修炼过了，休息一下吧！再过".($next_time+$ts[GAME_USERID]-$now)."秒可以再次修炼";
        }
        $ts[GAME_USERID]=$now;
        file_put_contents($file,json_encode($ts));
        $userStatus=player::get();
        $num=rand(0,5);
        if($num==0){
            return "修炼失败";
        }
        $level=level::get($userStatus["level"]);
        $nextLevel=level::get($userStatus["level"]+1);
        $userStatus["level_percent"]+=$num;
        $str="";
        if($userStatus["level_percent"]>100){
            $userStatus["level"]++;
            $userStatus["level_percent"]=0;
            $str="恭喜你，你已升阶！当前境界为：".$userStatus["level"]."级。";
        }
        player::set($userStatus);
        $str.= "本次修炼获得".$num."%进度，你的当前修为：".$userStatus["level"]."，进度：".$userStatus["level_percent"];
        return $str;
    }
}
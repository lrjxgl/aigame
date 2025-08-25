<?php
class player{
    public static function get(){
        $playerStatus = [
            // === 基础属性 ===
            'id' => 1001, // 玩家唯一ID
            'name' => '青云子', // 玩家角色名
            'level' => 0, // 当前境界等级 (练气期1层)
            'level_percent' => 0, // 当前等级进度 (0-100)

            'experience' => 0, // 当前经验值
            'exp_to_next_level' => 100, // 升级所需经验值
            'cultivation_stage' => 'LianQi', // 修炼境界 (LianQi: 练气, ZuoJi: 筑基, JinDan: 金丹, YuanYing: 元婴, etc.)
            'cultivation_stage_name' => '练气期', // 境界名称 (用于显示)
            'title' => '无名散修', // 称号
        
            // === 核心状态 ===
            'health' => 100, // 生命值 (HP)
            'max_health' => 100, // 最大生命值
            'mana' => 50, // 灵力值 (MP/灵力)
            'max_mana' => 50, // 最大灵力值
            'energy' => 100, // 精力值 (用于行动、采集等)
            'max_energy' => 100, // 最大精力值
            'stamina' => 80, // 体力值 (用于战斗、奔跑)
            'max_stamina' => 80, // 最大体力值
        
            // === 修炼属性 ===
            'cultivation_speed' => 1.0, // 修炼速度 (倍率)
            'spiritual_root' => 'Wind', // 灵根属性 (WuXing: 五行, Wind, Ice, Fire, Lightning, etc.)
            'spiritual_root_name' => '风灵根', // 灵根名称
            'talent' => 75, // 修炼天赋 (影响升级速度、技能领悟等)
            'karma' => 0, // 功德/业力值 (影响心境、天劫等)
        
            // === 战斗属性 ===
            'attack' => 20, // 物理攻击力
            'magic_attack' => 25, // 法术攻击力
            'defense' => 15, // 物理防御力
            'magic_defense' => 18, // 法术防御力
            'speed' => 30, // 速度 (影响行动顺序、闪避)
            'critical_rate' => 5.0, // 暴击率 (%)
            'critical_damage' => 150.0, // 暴击伤害 (%)
            'dodge_rate' => 8.0, // 闪避率 (%)
            'block_rate' => 3.0, // 格挡率 (%)
            'life_steal' => 0.0, // 吸血 (%)
            'mana_steal' => 0.0, // 吸蓝 (%)
        
            // === 资源属性 ===
            'gold' => 500, // 金币
            
            'spirit_stones' => 100, // 灵石
            'prestige' => 0, // 声望
            'contribution' => 0, // 门派贡献度
        
            
             
            'last_login' => time(), // 上次登录时间戳
            'play_time' => 0, // 累计游戏时长 (秒)
        
            // === 位置与探索 ===
            'map'=>"新手村",
            "school"=>"",//宗门
            "family"=>"",//家族
             
            'place' => '青云峰' // 当前位置
             
        
            
        ];
        $file=ROOT_PATH."/data/npc/user/".GAME_USERID.".json";
        if(!file_exists($file)){
            //保存当前数据
            file_put_contents($file,json_encode($playerStatus));
        }
        $player=json_decode(file_get_contents($file),true);
        if(!isset($player["map"])){
            $player["map"]="新手村";
        }
        return $player;
    }

    public static function set($playerStatus){ 
        $file=ROOT_PATH."/data/npc/user/".GAME_USERID.".json";
        //保存当前数据
        file_put_contents($file,json_encode($playerStatus));
        return true;
    }
    public static function getAll(){
        $dir=ROOT_PATH."/data/npc/user/";
        $files=glob($dir."*.json");
       
        $npcs=[];
        foreach ($files as $file) {
            $npc=json_decode(file_get_contents($file),true);
            $npcs[]=$npc;
        }
        return $npcs;
    }
    public static function getRank(){
        $npcs=self::getAll();
        
        usort($npcs,function($a,$b){
            return $b["level"]-$a["level"];
        });
        $npcs=array_slice($npcs,0,10);
        return $npcs;
    }

}

<?php
class npc{
    public static $attr=[
        'name'=>'npc',
        'level' => 1, // 当前境界等级 (练气期1层)
        'level_percent' => 0, // 当前等级进度 (0-100)
        'danLevel' => 0, // 炼丹等级 (1-9)
        'hp' => 100, // 生命值 (HP)
        'max_hp' => 100, // 最大生命值
        'mp' => 50, // 灵力值 (MP/灵力)
        'max_mp' => 50, // 最大灵力值
        'energy' => 100, // 精力值 (用于行动、采集等)
        'max_energy' => 100, // 最大精力值
        'stamina' => 80, // 体力值 (用于战斗、奔跑)
        'max_stamina' => 80, // 最大体力值
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
        'gold' => 500, // 金币            
        'spirit_stones' => 100, // 灵石
        'prestige' => 0, // 声望
        'map'=>"新手村",
        "school"=>"",//宗门
        "family"=>"",//家族
            
        'place' => '青云峰' // 当前位置
    ];

    public static function getAll(){
        $dir=ROOT_PATH."/data/npc/npc/";
        $files=glob($dir."*.json");
       
        $npcs=[];
        foreach ($files as $file) {
            $npc=json_decode(file_get_contents($file),true);
            $npcs[]=$npc;
        }
        return $npcs;
    }

    public static function getByName($name){
        $key=md5($name);
        $file=ROOT_PATH."/data/npc/npc/".$key.".json";
        if(!file_exists($file)){
            $attr=self::$attr;
            $attr["name"]=$name;
            file_put_contents($file,json_encode([$attr]));
        }
        return json_decode(file_get_contents($file),true);
    }

    /**
 * 生成指定等级的 NPC
 * @param int $level 要生成的等级 (1-100)
 * @return array 生成的 NPC 属性
 */
    public static function add($name,$level=1,$savefile=true) {
         
        $baseAttr = self::$attr;
        // 属性增长系数（可调）
        //$multiplier = $level; // 线性增长
        $multiplier = pow($level, 1.1); // 轻微指数增长，让高等级更强

        $npc = [
            'name'=>$name,
            'level' => $level,
            'level_percent' => mt_rand(0, 99), // 随机进度
            'health' => (int)($baseAttr['max_health'] * $multiplier),
            'max_health' => (int)($baseAttr['max_health'] * $multiplier),
            'mana' => (int)($baseAttr['max_mana'] * $multiplier),
            'max_mana' => (int)($baseAttr['max_mana'] * $multiplier),
            'energy' => (int)($baseAttr['max_energy'] * $multiplier),
            'max_energy' => (int)($baseAttr['max_energy'] * $multiplier),
            'stamina' => (int)($baseAttr['max_stamina'] * $multiplier),
            'max_stamina' => (int)($baseAttr['max_stamina'] * $multiplier),
            'attack' => (int)($baseAttr['attack'] * $multiplier),
            'magic_attack' => (int)($baseAttr['magic_attack'] * $multiplier),
            'defense' => (int)($baseAttr['defense'] * $multiplier),
            'magic_defense' => (int)($baseAttr['magic_defense'] * $multiplier),
            'speed' => (int)($baseAttr['speed'] * min(2.0, $multiplier * 0.8)), // 速度增长稍慢，避免失衡
            'critical_rate' => min(50.0, $baseAttr['critical_rate'] + ($level - 1) * 0.3), // 每级+0.3%，上限50%
            'critical_damage' => min(300.0, $baseAttr['critical_damage'] + ($level - 1) * 1.0), // 每级+1%，上限300%
            'dodge_rate' => min(40.0, $baseAttr['dodge_rate'] + ($level - 1) * 0.3),
            'block_rate' => min(25.0, $baseAttr['block_rate'] + ($level - 1) * 0.2),
            'life_steal' => min(20.0, ($level >= 10 ? 0.5 : 0) + ($level >= 20 ? 0.5 : 0) + ($level >= 30 ? 1.0 : 0)), // 高等级才出现吸血
            'mana_steal' => min(20.0, ($level >= 15 ? 0.5 : 0) + ($level >= 25 ? 0.5 : 0) + ($level >= 35 ? 1.0 : 0)),
        ];

        // 随机微调（±5%浮动）
        foreach ($npc as $key => $value) {
            if (is_numeric($value) && !in_array($key, ['level', 'level_percent'])) {
                $factor = 0.95 + (mt_rand(0, 100) / 1000); // 0.95 ~ 1.05
                $npc[$key] = round($value * $factor);
            }
        }
        if($savefile){
            $key=md5($name);
            $file=ROOT_PATH."/data/npc/npc/".$key.".json";
             file_put_contents($file,json_encode($npc));
        }
        
        return $npc;
    }

    public static function addLevel($npc,$level_percent=1){
        $level=level::get($npc["level"]);
        $nextLevel=level::get($npc["level"]+1);
        $npc["level_percent"]+=$level_percent;
        $str="";
        if($npc["level_percent"]>100){
            $npc["level"]++;
            $npc["level_percent"]=0;
            $str="恭喜你，你已升阶！当前境界为：".$nextLevel["name"]."。";
        }
        return ["npc"=>$npc, "msg"=>$str];
    }

}
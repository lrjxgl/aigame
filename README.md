# aigame
利用ai来生成游戏场景，互动，对话,战斗等
当前项目展示是一个修仙者的ai文字游戏。
玩家可以进行地图探索，采摘，购买，修炼、战斗等等。

一、直接在php环境，无需数据库，即可运行 
PHP -S localhost:8080
当然nginx、apache也可以

二、配置AI：
api/config/ai/apicom_config.php 
默认使用modelscope，在ai.php中修改

三、ai角色提示词 /api/config/ai/system.txt


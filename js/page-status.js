Vue.component('page-status', {
    data(){
        return {
            data:{}
        }
    },
    created(){
        let u=localStorage.getItem('user');
        let userid=JSON.parse(u).userid;
        fetch("api/data.php?action=status&userid="+userid)
            .then(res=>res.json())
            .then(res=>{

                this.data = res.data
            })
    },
    template: `
    <div id="beibao"> 
        <div class="row-box">
            <div class="flex flex-wrap " >
                 <span class="pd-5 mg-5">玩家名字：{{data.name}}</span>
                <span class="pd-5 mg-5">修为：{{data.level}} 进度：{{data.level_percent}}%</span>
                <span class="pd-5 mg-5">地图：{{data.map}}</span>
                <span class="pd-5 mg-5">地点：{{data.place}}</span>
                <span class="pd-5 mg-5">物理攻击：{{data.attack}}</span>
                <span class="pd-5 mg-5">魔法攻击：{{data.magic_attack}}</span>
                <span class="pd-5 mg-5">物理防御：{{data.defense}}</span>
                <span class="pd-5 mg-5">魔法防御：{{data.magic_defense}}</span>
                <span class="pd-5 mg-5">金币：{{data.gold}}</span>
                <span class="pd-5 mg-5">灵石：{{data.spirit_stones}}</span>
            </div>
        </div>
        
    </div>
    `
})
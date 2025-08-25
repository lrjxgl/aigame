Vue.component('page-rank', {
    data(){
        return {
            list:[]
        }
    },
    created(){
        let u=localStorage.getItem('user');
        let userid=JSON.parse(u).userid;
        fetch("api/data.php?action=rank&userid="+userid)
            .then(res=>res.json())
            .then(res=>{

                this.list = res.list
            })
    },
    template: `
    <div > 
        <div class="row-box">
            <div class="bd-mp-10" v-for="(item,index) in list" :key="index">
                <div class="cl-primary">{{item.name}}</div>
                <div>
                    <span class="cl2">等级 {{item.level}}</span>
                    <span class="cl2">生命值 {{item.max_health}}</span>
                    <span class="cl2">物理攻击 {{item.attack}}</span>
                    <span class="cl2">魔法攻击 {{item.magic_attack}}</span>
                    <span class="cl2">物理防御 {{item.defense}}</span>
                    <span class="cl2">魔法防御 {{item.magic_defense}}</span>
                     
                </div>
            </div> 
        </div>
        
    </div>
    `
})
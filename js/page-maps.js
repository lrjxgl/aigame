Vue.component('page-maps', {
    data(){
        return {
            list:[]
        }
    },
    created(){
        let u=localStorage.getItem('user');
        let userid=JSON.parse(u).userid;
        fetch("api/data.php?action=maps&userid="+userid)

            .then(res=>res.json())
            .then(res=>{

                this.list = res.list
            })
    },
    template: `
    <div id="beibao"> 
        <div class="row-box">
           <div class="bd-mp-10" v-for="(item,index) in list" :key="index">
                <div class="flex">  
                    <div class="cl-primary">{{item.name}}</div>
                    <div class="tag mgl-10">等级：{{item.level}}</div>
                    <div class="tag mgl-10">进入等级：{{item.min_level}}</div>
                </div>
                <div class="cl2">{{item.description}}</div>
            </div>    
        </div>
        
    </div>
    `
})
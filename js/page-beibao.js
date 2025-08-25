Vue.component('page-beibao', {
    data(){
        return {
            list:[],
            toastContent:"",
            toastShow:false
        }
    },
    created(){
        this.getPage();
    },
    methods:{
        getPage(){
            let userid=this.getuserid();
            fetch("api/data.php?action=beibao&userid="+userid)
                .then(res=>res.json())
                .then(res=>{

                    this.list = res.list
                })
        },
        getuserid(){
            let u=localStorage.getItem('user');
            let userid=JSON.parse(u).userid;
            return userid
        },
        sell(title){
            let userid=this.getuserid();
            fetch("api/data.php?action=beibao_sell&userid="+userid+"&title="+title)
                .then(res=>res.json())
                .then(res=>{
                    this.getPage();
                    
                        
                })
        },
        use(title){
            let userid=this.getuserid();
            fetch("api/data.php?action=beibao_use&userid="+userid+"&title="+title)
                .then(res=>res.json())
                .then(res=>{
                    this.getPage();
                })
        },
        delete(title){
            let userid=this.getuserid();
            fetch("api/data.php?action=beibao_delete&userid="+userid+"&title="+title)
                .then(res=>res.json())
                .then(res=>{
                    this.getPage();
                }) 
        },
        xiulian(gongfa){
            let userid=this.getuserid();
            fetch("/api/data.php?action=gongfa_xiulian&name="+gongfa+"&userid="+userid)
            .then(res=>res.json())
            .then(res=>{
                this.toastContent=res.data;
                this.toastShow=true;
                setTimeout(()=>{
                    this.toastShow=false;
                },3000)
                this.getPage();
            })
        }

    },
    template: `
    <div id="beibao"> 
        <div class="toast" v-if="toastShow">{{toastContent}}</div>
        <div class="row-box">
            <div class="flex bd-mp-10 "  v-for="(item,index) in list" :key="index">
                <div>{{item.title}} x{{item.num}}</div>
                <div v-if="item.type=='gongfa'" class="tag mgl-5">修炼等级 {{item.ulevel}}</div>
                <div class="flex-1"></div>
                <div @click="sell(item.title)" class="btn-mini mgr-10">出售</div>
                <div v-if="item.type=='good'" @click="use(item.title)" class="btn-mini mgr-10">使用</div>
                <div v-if="item.type=='gongfa'" @click="xiulian(item.title)" class="btn-mini mgr-10">修炼</div>
               
            </div>
        </div>
        
    </div>
    `
})
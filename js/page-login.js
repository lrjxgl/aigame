Vue.component('page-login', {
    data(){
        return {
            user:{},
            toastContent:"",
            toastShow:false
        }
    },
    methods:{
        login(){
            fetch("api/login.php",{
                method:'POST',
                body:JSON.stringify(this.user)
            })
            .then(res=>res.json())
            .then(res=>{
                
                if(res.error){
                    this.toastShow=true;
                    this.toastContent=res.message;
                    setTimeout(()=>{
                        this.toastShow=false;
                    },1000)
                    return false;
                }
                localStorage.setItem('user',JSON.stringify(res.user));
                 
                this.$emit('login-success',1);
            })
        }
    },
    template: `
    <div id="login">
    
        <div class="modal-mask"></div>
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">登录</div>
            </div>
            <div class="modal-body">
                 <div class="row-box">
                    <div class="input-flex">
                        <label class="input-flex-label">用户名</label>
                        <input class="input-flex-text" type="text" v-model="user.name">
                    </div>
                    <div class="input-flex">
                        <label class="input-flex-label">密码</label>
                        <input class="input-flex-text" type="password" v-model="user.password">
                    </div>
                    <button class="btn-row-submit" @click="login">登录</button>
                </div>  
            </div>
        </div>
        <div class="toast" v-if="toastShow">{{toastContent}}</div>     
    </div>
    `
})          

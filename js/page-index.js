Vue.component('page-index', {

    data(){
        return {
            message: 'Hello Vue!',
            msgcon: "",
            msgList: [],
            chatList:[],
            wsclient_to: "aigame",
            ws: false,
            stream: 1,
            wsContent: "",
            wstaskid: "",
            inpost: false,
            inTask:false,
            userid:0,
            aicontrol:false,
            aitimer:0,
            cmds:["帮助","修炼","探索","采摘","战斗","clear"]
        }
    },

    watch:{
        msgList:function(n,o){
            let chatList=[];
            for(var i in n){
                if(n[i].content==null){
                    n[i].content="无回应"
                } 
                n[i].content=n[i].content.replace(/\n/g,'<br>');
                chatList.push(n[i]);
            }
            this.chatList=chatList;
        }
    },
    created() {
        this.wsclient_to = "aigame" + Date.now();
        this.wsinit();
        this.getPage();
    },
    methods: {
        getPage() {
            fetch_get("api/data.php?action=gamedata",{},(res)=>{
                this.msgList = res.list;
                this.goBottom();
            })
             
        },
        aiwork(){
            let u=localStorage.getItem('user');
            if(!u) return false;
            let userid=JSON.parse(u).userid;
            if(this.inTask || this.inpost){
                this.aitimer=setTimeout(() => {
                    this.aiwork();
                }, 10000);
            }
            fetch("api/data.php?action=aiwork&userid="+userid)
                .then(res=>res.json())
                .then(res=>{    
                    if(!empty(res.prompt)){
                        this.msgcon=res.prompt;
                        this.send();
                    }
                    
                    this.aitimer=setTimeout(() => {
                        this.aiwork();
                    }, 600000);

                     
                })
            
        },
        wsinit() {
            var that = this;
            var ws = new WebSocket("wss://wss.deituicms.com:8282/");
            this.ws = ws;
            ws.onopen = function (e) {
                console.log(e)
                ws.send(JSON.stringify({
                    k: that.wsclient_to,
                    type: "login"
                }))
                setInterval(function () {
                    ws.send(JSON.stringify({

                        type: "ping"
                    }))
                }, 50000)
            }
            ws.onmessage = function (e) {


                var res = JSON.parse(e.data);
                
                switch (res.type) {
                    case "say":

                        that.inpost = false;
                        
                        that.wsContent += res.content;
                        if(typeof(that.$refs.wsLog)=='undefined') return false;

                        that.$refs.wsLog.scrollTo(0, that.$refs.wsLog.scrollHeight);
                        

                        break;

                }


            }
            ws.close = function (e) {
                console.log(e);
            }
        },
        send() {
            if(this.msgcon=='注销'){
                localStorage.removeItem('user');
                this.$emit('unlogin',1);
                this.msgcon="";
                return false;
            }
            if(this.msgcon=='开启AI'){
                this.aicontrol=true;
                this.aiwork();
                this.msgcon="";
                return false;
            }
            if(this.msgcon=='关闭AI'){
                this.aicontrol=false;
                clearTimeout(this.aitimer);
                this.msgcon="";
                return false;
            }
            this.wsContent = "";
            if (this.inpost) {
                return false;
            }
            this.inpost = true;
            this.inTask=true;
            var fdata = new FormData();
            fdata.append("prompt", this.msgcon);
            fdata.append("stream", this.stream);
            fdata.append("wsclient_to", this.wsclient_to)
            fdata.append("wstaskid", this.wstaskid)
            fdata.append("history", JSON.stringify(this.msgList));
            let u=localStorage.getItem('user');
             
            fdata.append("userid",JSON.parse(u).userid);
            this.msgList.push({
                content: this.msgcon,
                role: "user"
            });
            this.msgList.push({
                content: "正在努力思考...",
                role: "thinking"
            });
            this.$nextTick(()=>{
                this.$refs.messages.scrollTo(0, this.$refs.messages.scrollHeight);
            })
            fetch("api/input.php", {
                method: "POST",
                body: fdata

            }).then(res => res.json()).then(res => {
                 
                let msg = {
                    role: "assistant",
                    content: res.data.content,
                }
                console.log(this.msgList)
                let newList = [];
                for (var i in this.msgList) {
                    if (this.msgList[i].role != "thinking") {
                        newList.push(this.msgList[i]);
                    }
                }
                newList.push(msg);
                this.msgList = newList;
                this.inpost = false;
                this.inTask=false;
               
                
                if(this.msgcon=="clear"){
                    this.msgList=[];
                     
                }
                this.msgcon = ""
                this.goBottom();
            })
        },
        dbSend(msg){
            this.msgcon=msg;
            this.send();
        },
        goBottom(){
            //body 滚动条 滚动到底部
            this.$nextTick(()=>{
                console.log("滚动到底部")
                document.documentElement.scrollTop = 100000;
            })

        }
    },
    template: `
        <div>
            <div v-if="inTask">
                <div class="modal-mask"></div>
                <div v-if="wsContent!=''"  ref="wsLog" class="wsLog">{{wsContent}}</div>
                <div class="wsLog" v-else>正在努力思考...</div>
            </div>
            
            <div class="messages-container" id="messages" ref="messages">
                <div v-if="msgList.length==0">
                    <div class="message received">
                        <div class="message-content">需要帮助，请输入帮助</div>
                      

                    </div>
                </div>
                <!-- 消息将在这里动态添加 -->
                <div v-for="(item,index) in msgList" :key="index" class="message received">
                    <div @click="dbSend(item.content)" v-if="item.role=='user'" class="message-content pointer" v-html="item.content"></div>
                    <div v-else class="message-content" v-html="item.content"></div>
   
                </div>
               
            </div> 
            <div style="height:70px;"></div>
            <div class="input-container">
                <input @keyup.enter="send" type="text" class="message-input" v-model="msgcon" placeholder="输入命令..." autocomplete="off">
                <button class="send-button" @click="send()">
                    发送
                </button>
            </div>

            <div class="cmdBox">
                <div v-for="(item,index) in cmds" :key="index" @click="dbSend(item)" class="cmdBox-item">{{item}}</div>
                 
            </div>
        </div>
    `

})
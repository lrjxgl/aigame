var App=new Vue({
  el: '#App',
  data(){
    return {
      page:'index'
    }
  },
  created(){
      var user=localStorage.getItem('user');
      if(!user){
        this.page='login';
        return;
      }
  },
  watch:{
    page(){
      this.$nextTick(()=>{
        //滚动到底部
        this.goBottom();
        
      })
    }
  },
  methods:{
    getPage(){

    }, 
    goBottom(){
        //body 滚动条 滚动到底部
        this.$nextTick(()=>{
            console.log("滚动到底部")
            document.documentElement.scrollTop = 100000;
        })

    }
  }  

})
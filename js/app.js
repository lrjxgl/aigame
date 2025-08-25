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
  methods:{
    getPage(){

    }, 
  }  

})
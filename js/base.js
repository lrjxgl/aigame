function getuserid(){
    let u=localStorage.getItem('user');
    if(u==null) return "";
    let userid=JSON.parse(u).userid;
    return userid
}
function get_ai_config(){
    let res=localStorage.getItem('ai_config');
    if(res==null) return "";
    return res
}
function set_ai_config(e){
    localStorage.setItem('ai_config',JSON.stringify(e))
}
/**
 * get方式获取数据
 */
function fetch_get(url,params = {},callback){
     
    let allParams = {
        ...params,
        userid: getuserid(),
        ai_config: get_ai_config(),
      };
    let queryString = new URLSearchParams(allParams).toString();
    if(url.indexOf("?")==-1){
        url+="?";
    } 

    url +="&"+queryString; 
    fetch(url)
        .then(res=>res.json())
        .then(res=>{    
            callback(res);
        })
}

/**
 * post方式获取数据

 */
function fetch_post(url,data,callback,headers={}){
    if(url.indexOf("?")==-1){
        url+="?";
    } 
    url+="&userid="+getuserid();
    url+="&ai_config="+get_ai_config();
    fetch(url,{
        method:"POST",
        headers:headers,
        body:JSON.stringify(data),
    })
        .then(res=>res.json())
        .then(res=>{
            callback(res)
        })
}

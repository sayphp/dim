var dim = {

    host:null,

    port:null,

    session:null,

    ws:null,

    sign:function(admin_session){
        var info = {
            act: 'admin',
            method: 'sign',
            sess: admin_session,
        };
        dim.send(JSON.stringify(info));
    },

    conn:function(){
        dim.ws = new WebSocket('ws://'+dim.host+':'+dim.port);
        return true;
    },

    send:function(msg){
        console.log(dim);
        dim.ws.send(msg);
    },

    listen:function(){
        dim.ws.open = function(e){
            console.log('连接成功');
        };
        dim.ws.message = function(e){
            console.log(e);
        }
    },


    set:function (obj) {
        if(typeof (obj.host)!=undefined) dim.host = obj.host;
        if(typeof (obj.port)!=undefined) dim.port = obj.port;
    },

    start:function(){
        console.log('启动');
        dim.conn();
        dim.sign(1);

        console.log(dim);
    }
};
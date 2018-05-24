<?php
    /*
     * websocket测试脚本
     * say
     * 2018-05-07
     */

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>dim js websocket测试demo</title>
    </head>
    <body>
        <div id="chat">

        </div>
    </body>
    <script type="text/javascript" src="/sdk/dim.sdk.js"></script>
    <script type="text/javascript">
        dim.set({host:'127.0.0.1', port:9501});
        dim.start();
        //var session;
        // var ws = new WebSocket('ws://127.0.0.1:9501');
        // var ws = new WebSocket('ws://47.93.33.2:9501');
        // ws.open = function(e){
        //     console.log('连接成功');
        // };
        // ws.onmessage = function(e){
        //     var d = JSON.parse(e.data);
        //     // console.log(d);
        //     if(d.status!=0){
        //         console.log(d.error);
        //         return ;
        //     }
        //     switch(d.act){
        //         case 'admin':
        //             switch(d.method){
        //                 case 'sign'://登陆
        //                     session = d.data.session;
        //                     break;
        //                 case 'msg'://收到消息
        //                     console.log(d.data.msg);
        //                     break;
        //                 default:
        //                     console.log('未预定义方法');
        //                     console.log(d);
        //             }
        //             break;
        //         default:
        //             console.log('未预定义服务');
        //             console.log(d);
        //     }
        // }
        // ws.onerror = function(e) { console.log(e); };
        // ws.onclose = function(e){ console.log(e); }
        // function sign(sess){
        //     var info = {
        //         act:'admin',
        //         method:'sign',
        //         sess: sess,
        //     };
        //     ws.send(JSON.stringify(info));
        // }
        // function send(msg, sess){
        //     var info = {
        //         act:'admin',
        //         method:'msg',
        //         to: sess,
        //         msg: msg,
        //         session:session,
        //     };
        //     console.log(info);
        //     ws.send(JSON.stringify(info));
        // }

    </script>
</html>
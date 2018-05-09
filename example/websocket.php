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
    <!--
    <script type="text/javascript" src="/sdk/dim.sdk.js"></script>
    -->
    <script type="text/javascript">
        var session;
        var ws = new WebSocket('ws://127.0.0.1:9501');
        //var ws = new WebSocket('ws://118.25.40.163:8088');
        ws.open = function(e){
            console.log('连接成功');
        };
        ws.onmessage = function(e){
            var d = JSON.parse(e.data);
            console.log(d);
            if(d.status!=0){
                console.log(d.error);
                return ;
            }
            if(typeof(d.data.session)!=undefined){
                session = d.data.session;
            }
            console.log(d);
        }
        ws.onerror = function(e) { console.log(e); };

        function sign(sess){
            var info = {
                act:'admin',
                method:'sign',
                sess: sess,
            };
            ws.send(JSON.stringify(info));
        }
        function send(msg, sess){
            var info = {
                act:'admin',
                method:'msg',
                to: sess,
                msg: msg,
                session:session,
            };
            console.log(info);
            ws.send(JSON.stringify(info));
        }

    </script>
</html>
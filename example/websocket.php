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
         var ws = new WebSocket('ws://127.0.0.1:9501');
        //var ws = new WebSocket('ws://118.25.40.163:8088');
        ws.open = function(e){
            console.log('连接成功');
        };
        ws.onmessage = function(e){
            console.log('收到消息');
            console.log(e);
        }
        ws.onerror = function(e) { console.log(e); };
        var info = {
            act:'admin',
            method:'sign',
        };

    </script>
</html>
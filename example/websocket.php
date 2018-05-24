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
        <div id="user">
        用户名：<input id="name" type="name" />
        <button onclick="sign();">登录</button>
        </div>
        <div id="chat">
            <div id="msg_list" style="width:400px;height:200px;overflow-y:scroll;border:1px solid #EEE">
            </div>
            <input id="msg" type="name" value="" /><button id="btn" onclick="mass();">发言</button>
        </div>
    </body>
<!--    <script type="text/javascript" src="/sdk/dim.sdk.js"></script>-->
    <script type="text/javascript">
        // dim.set({host:'127.0.0.1', port:9501});
        // dim.start();
        var member_id;
        var session;
        var ws = new WebSocket('ws://192.168.1.124:9501');
        // var ws = new WebSocket('ws://47.93.33.2:9501');
        ws.open = function(e){
            console.log('连接成功');
        };
        ws.onmessage = function(e){
            var d = JSON.parse(e.data);
            // console.log(d);
            if(d.status!=0){
                console.log(d.error);
                return ;
            }
            switch(d.act){
                case 'zhishi':
                    switch(d.method){
                        case 'sign'://登陆
                            console.log(d);
                            session = d.data.session;
                            alert('登陆成功，您可以开始聊天了');
                            document.getElementById('msg').focus();
                            document.getElementById('user').style.display = "none";
                            break;
                        case 'msg'://收到消息
                        case 'mass'://收到群发
                            console.log(d.data);
                            var html = document.getElementById("msg_list").innerHTML;
                            document.getElementById("msg_list").innerHTML = html + "<p>"+d.data.msg+"</p>";
                            document.getElementById("msg_list").scrollTop = document.getElementById("msg_list").scrollHeight;
                            break;
                        default:
                            console.log('未预定义方法');
                            console.log(d);
                    }
                    break;
                default:
                    console.log('未预定义服务');
                    console.log(d);
            }
        }
        ws.onerror = function(e) { console.log(e); };
        ws.onclose = function(e){ console.log(e); }
        function sign(){
            member_id = document.getElementById('name').value;
            console.log(member_id);
            if(member_id==''){
                alert('用户名不得为空');
                return;
            }
            var info = {
                act:'zhishi',
                method:'sign',
                member_id: member_id,
            };
            ws.send(JSON.stringify(info));
        }
        function mass(){
            if(typeof(session)==undefined){
                alert('请先登录');
                return;
            }
            var msg = document.getElementById('msg').value;
            if(msg==''){
                alert('信息不能为空');
                return;
            }
            var info = {
                act:'zhishi',
                method:'mass',
                msg: member_id + "说："+ msg,
                session:session,
            };
            console.log(info);
            ws.send(JSON.stringify(info));
            document.getElementById('msg').value = '';
        }
        document.onkeydown=function(event){
            var e = event || window.event || arguments.callee.caller.arguments[0];
            if(e && e.keyCode==27){ // 按 Esc
                //要做的事情
            }
            if(e && e.keyCode==113){ // 按 F2
                //要做的事情
            }
            if(e && e.keyCode==13){ // enter 键
                document.getElementById('btn').click();
            }
        };
    </script>
</html>
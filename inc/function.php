<?php
    /**
     * 公用方法
     * say
     * 2018-05-01
     */

    function error($code, $msg=null){
        $msg = $msg?$msg:conf::error($code);
        throw new Exception($msg, $code);
    }

    function close($fd, $code, $msg=null){
        dim::$server->close($fd);
        error($code, $msg);
    }

    //通讯id
    function uid($fd){
        return md5(raft::id().'_'.$fd);
    }

    //客户端识别
    function session($fd, $act){
        return md5($act.'_'.$fd.'_'.time());
    }
    //服务间请求接口
    function request($id, $info){
        $client = new swoole_client(SWOOLE_SOCK_TCP);
        $rs = @$client->connect(conf::$server[$id]['host'], conf::$server[$id]['port']);
        if(!$rs) return false;
        //接受链接uid
        $str = $client->recv();
        $data = json_decode($str, 1);
        if(!$data) return false;
        if($data['status']!=0) return false;
        $info['uid'] = $data['data']['uid'];
        //注册
        $rs = $client->send(json_encode(askServ::sign($info['uid'], $id)));
        $str = $client->recv();
        $data = json_decode($str, 1);
        if(!$data) return false;
        if($data['status']!=0) return false;
        $info['session'] = $data['data']['session'];
        //*发送请求
        $rs = $client->send(json_encode($info));
        if(!$rs) return false;
        $str = $client->recv();
        $data = json_decode($str, 1);
        $client->close();
        return $data;
    }
    //ini写入
    function ini_write($type, $name, $ini){
        $content = '';
        foreach($ini as $k => $v){
            $content .= $k.'='.$v.PHP_EOL;
        }
        $fp = fopen(ROOT.'conf/'.$type.'/'.$name.'.ini', 'w+');
        $rs = fwrite($fp, $content);
        return $rs;
    }
    //目录文件并更
    function update_lists(){
        $data = [];
        $range = '{'.ROOT.'app/*.app.php,'.ROOT.'task/*.task.php,'.ROOT.'conf/code/*.ini}';
        $file_lists = glob($range, GLOB_BRACE);
        foreach($file_lists as $file){
            $data[$file] = filemtime($file);
        }
        return $data;
    }
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
        //注册
        $rs = $client->send(json_encode(askServ::sign($id)));
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
        fclose($fp);
        return $rs;
    }
    //文件写入
    function file_write($pathname, $content=''){
        $fp = fopen($pathname, 'w+');
        $rs = fwrite($fp, $content);
        fclose($fp);
        return $rs;
    }
    //目录文件并更
    function update_lists(){
        $data = [];
        $range = '{'.ROOT.'app/*.app.php,'.ROOT.'task/*.task.php,'.ROOT.'conf/code/*.ini}';
        $file_lists = glob($range, GLOB_BRACE);
        foreach($file_lists as $file){
            $data[str_replace(ROOT, '', $file)] = [
                'md5' => md5_file($file),
                'mtime' => filemtime($file),
            ];
        }
        return $data;
    }


    //websocket解码
    function decode($received){
        $len = $masks = $data = $decoded = null;
        $buffer = $received;
        $len = ord($buffer[1]) & 127;
        if ($len === 126) {
            $masks = substr($buffer, 4, 4);
            $data  = substr($buffer, 8);
        } else {
            if ($len === 127) {
                $masks = substr($buffer, 10, 4);
                $data  = substr($buffer, 14);
            } else {
                $masks = substr($buffer, 2, 4);
                $data  = substr($buffer, 6);
            }
        }
        for ($index = 0; $index < strlen($data); $index++) {
            $decoded .= $data[$index] ^ $masks[$index % 4];
        }

        return $decoded;
    }

    //websocket编码
    function encode($buffer){
        $len = strlen($buffer);
        $first_byte = "\x81";

        if ($len <= 125) {
            $encode_buffer = $first_byte . chr($len) . $buffer;
        } else {
            if ($len <= 65535) {
                $encode_buffer = $first_byte . chr(126) . pack("n", $len) . $buffer;
            } else {
                //pack("xxxN", $len)pack函数只处理2的32次方大小的文件，实际上2的32次方已经4G了。
                $encode_buffer = $first_byte . chr(127) . pack("xxxxN", $len) . $buffer;
            }
        }

        return $encode_buffer;
    }
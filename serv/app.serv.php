<?php
    /**
     * app.serv.php
     * 服务应用
     * say
     * 2018-04-28
     */
    class appServ{
        //服务自检
        public static function check(){}
        //服务状态
        public static function status(){
            //1.检查当前服务器情况
            //$status = dim::$server->stats();
            var_dump(conf::lists(raft::id()));
            if(raft::id()==raft::leader()){//2.当前服务器是否leader
                conf::set(raft::id(), 'status', 1);
                foreach(conf::lists() as $id => $ini){
                    if($id==raft::id()) continue;
                    if(!$ini['status']) continue;
                    $data = request($id, askRaft::term());
                }
            }else{
                $info = conf::lists(raft::id());

                if($info['status']==0){//3.未加入集群
                    $data = request(raft::leader(), askServ::join());
                    if($data && $data['status']==0){
                        conf::set(raft::id(), 'status', 1);
                        conf::set(raft::id(), 'timeout', $data['data']['timeout']);
                    }
                }elseif($info['status']==3){//4.等待重启中

                }elseif(raft::timeout()>time()){//4.是否要变成竞选者

                }
            }
            return true;
        }
        //服务数据落地
        public static function backup(){}
        //服务代码更新
        public static function update(){}
        //服务重加载
        public static function reload(){}
        //回复消息
        public static function reply(){}
        //群发消息
        public static function mass(){}
        //单发消息
        public static function send(){}
        //转发消息
        public static function forward(){}
        //加入集群
        public static function join($cid, $chost, $cport, $cpass, $lid, $lhost, $lport, $lpass){
            $id = raft::id();
            $leader = raft::leader();
            $client = conf::lists($cid);
            $server = conf::lists($leader);
            if($id!=$leader) error(1001);
            if($leader!=$lid) error(41);
            if($server['host']!= $lhost) error(42);
            if($server['port']!= $lport) error(43);
            if($server['pass']!= $lpass) error(44);
            var_dump($client, $server);
            if(in_array($client['status'],[1,3])) error(1002);
            conf::set($cid, 'host', $chost);
            conf::set($cid, 'port', $cport);
            conf::set($cid, 'pass', $cpass);
            conf::set($cid, 'status', 1);
            return strtotime('+6 s');//追加有效期
        }
        //leader信息
        public static function leader(){
            return raft::leader();
        }
        //登陆
        public static function sign($fd, $uid, $sid, $shost, $sport, $spass){
            $id = raft::id();
            if($sid!=$id) error(41);
            if(conf::$server[$id]['host']!=$shost) error(42);
            if(conf::$server[$id]['port']!=$sport) error(43);
            if(conf::$server[$id]['pass']!=$spass) error(44);
            $session = session($fd, 'server');
            $rs = dim::$mem->hset($uid, 'session', $session);
            return $session;
        }
    }
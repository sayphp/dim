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
            return dim::$server->stats();
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
        public static function join(){}
        //leader信息
        public static function leader(){
            return raft::$leader;
        }
        //登陆
        public static function sign($fd, $uid, $leader_pass, $id, $host, $port, $pass){
            if(conf::$server[raft::$leader]['pass']!=$leader_pass) error(32);
            if(conf::$server[$id]['status']==1) error(33);
            foreach(conf::$server as $k => $v){
                if($k==$id) continue;
                if($v['status']!=1) continue;
                if($v['host']==$host && $v['port']==$port) error(33);
            }
            if(raft::$current==raft::$leader){
                conf::$server[$id]['host'] = $host;
                conf::$server[$id]['port'] = $port;
                conf::$server[$id]['pass'] = $pass;
                conf::$server[$id]['status'] = 1;
            }
            $session = session($fd, 'server');
            $rs = dim::$mem->hset($uid, 'session', $session);
            return $session;
        }
    }
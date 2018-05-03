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
        public static function sign($fd, $uid, $sid, $shost, $sport, $spass){
            if($sid==raft::$id) error(41);
            if(conf::$server[raft::$id]['host']!=$shost) error(42);
            if(conf::$server[raft::$id]['port']!=$sport) error(43);
            if(conf::$server[raft::$id]['pass']!=$spass) error(44);
            $session = session($fd, 'server');
            $rs = dim::$mem->hset($uid, 'session', $session);
            return $session;
        }
    }
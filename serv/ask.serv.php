<?php
    /**
     * ask.serv.php
     * 服务请求
     * say
     * 2018-04-28
     */
    class askServ{
        //服务自检
        public static function check(){}
        //服务状态
        public static function status(){
            return [
                'act' => 'server',
                'method' => 'status',
            ];
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
            return [
                'act' => 'server',
                'method' => 'leader',
            ];
        }
        //登陆
        public static function sign($uid=0, $id=0){
            $id = $id?$id:raft::$id;
            $par = [
                'act' => 'server',
                'method' => 'sign',
                'uid' => $uid,
                'id' => $id,//本机ID
                'host' => raft::$current['host'],//本机
                'port' => raft::$current['port'],
                'pass' => raft::$current['pass'],
                'leader_pass' => conf::$server[raft::$leader]['pass'],
            ];
            return $par;
        }
    }
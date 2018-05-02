<?php
    /*
     * server.core.php
     * 服务类
     * Say
     * 2018-04-23
     */
    class server{

        //数据落地
        public static function bak(){
            $par = [
                'act' => 'server',
                'method' => 'bak',
            ];
            return $par;
        }
        //注册
        public static function reg($uid=0, $id=0){
            $id = $id?$id:raft::$id;
            $par = [
                'act' => 'server',
                'method' => 'sign',
                'uid' => $uid,
                'id' => $id,//本机ID
                'host' => raft::$current['host'],//本机
                'port' => raft::$current['port'],
                'pass' => raft::$current['pass'],
                'leader_pass' => raft::$servers[raft::$leader]['pass'],
            ];
            return $par;
        }
        //查找leader
        public static function leader(){
            $par = [
                'act' => 'server',
                'method' => 'leader',
            ];
            return $par;
        }
    }
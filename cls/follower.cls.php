<?php
    /**
     * follower.cls.php
     * 跟随者
     * say
     * 2018-04-07
     */
    class follower{

        //加入服务器集群
        public static function join(){
            $par = [
                'act' => 'server',
                'method' => 'leader',
            ];
            return $par;
        }
        //到leader注册
        public static function sign(){
            $par = [
                'act' => 'server',
                'method' => 'sign',
                'id' => raft::$id,
                'host' => raft::$current['host'],
                'port' => raft::$current['port'],
                'pass' => raft::$current['pass'],
                'leader_pass' => raft::$servers[raft::$leader]['pass'],
            ];
            return $par;
        }
        //跟随者任务处理
        public static function deal(){
            $par = [
                'act' => 'follower',
                'method' => 'deal',
            ];
            raft::$server->task($par);
            task::$follower = time();
        }
    }
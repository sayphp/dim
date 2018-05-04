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
        public static function join(){
            $id = raft::id();
            $leader = raft::leader();
            $client = conf::lists($id);
            $server = conf::lists($leader);
            return [
                'act' => 'server',
                'method' => 'join',
                'cid' => $id,
                'chost' => $client['host'],
                'cport' => $client['port'],
                'cpass' => $client['pass'],
                'lid' => $leader,
                'lhost' => $server['host'],
                'lport' => $server['port'],
                'lpass' => $server['pass'],
            ];
        }
        //leader信息
        public static function leader(){
            return [
                'act' => 'server',
                'method' => 'leader',
            ];
        }
        //登陆
        public static function sign($uid=0, $sid=0){
            $par = [
                'act' => 'server',
                'method' => 'sign',
                'uid' => $uid,
                'sid' => $sid,//服务ID
                'shost' => conf::$server[$sid]['host'],//服务
                'sport' => conf::$server[$sid]['port'],
                'spass' => conf::$server[$sid]['pass'],
            ];
            return $par;
        }
    }
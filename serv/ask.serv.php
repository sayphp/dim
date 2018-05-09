<?php
    /**
     * ask.serv.php
     * 服务请求
     * say
     * 2018-04-28
     */
    class askServ{
        //服务自检
        public static function check(){
            return [
                'act' => 'server',
                'method' => 'check',
            ];
        }
        //服务状态
        public static function status(){
            return [
                'act' => 'server',
                'method' => 'status',
            ];
        }
        //服务数据落地
        public static function backup(){
            return [
                'act' => 'server',
                'method' => 'backup',
            ];
        }
        //服务代码更新
        public static function update($cid){
            return [
                'act' => 'server',
                'method' => 'update',
                'cid' => $cid,
            ];
        }
        //服务重加载
        public static function reload(){
            return [
                'act' => 'server',
                'method' => 'reload',
            ];
        }
        //回复消息
        public static function reply(){
            return [
                'act' => 'server',
                'method' => 'reply',
            ];
        }
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
        public static function sign($sid=0){
            $par = [
                'act' => 'server',
                'method' => 'sign',
                'sid' => $sid,//服务ID
                'shost' => conf::$server[$sid]['host'],//服务
                'sport' => conf::$server[$sid]['port'],
                'spass' => conf::$server[$sid]['pass'],
            ];
            return $par;
        }
        //比对
        public static function diff(){
            return [
                'act' => 'server',
                'method' => 'diff',
                'lists' => update_lists(),
            ];
        }
        //升级
        public static function upgrade(){
            return [
                'act' => 'server',
                'method' => 'upgrade',
            ];
        }
    }
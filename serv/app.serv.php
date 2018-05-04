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

            dim::$tmp++;
            var_dump(dim::$tmp,'===============');
            $data = dim::$table->get(1);
            var_dump($data);
            $data['id']++;
            dim::$table->set(1, $data);
            //var_dump(conf::$server[raft::$id]['status'],raft::$timeout);
            //1.检查当前服务器情况
            $status = dim::$server->stats();
            if(raft::$id==raft::$leader){//2.当前服务器是否leader
                conf::$server[raft::$id]['status'] = 1;
                foreach(conf::$server as $id => $ini){
                    if($id==raft::$id) continue;
                    if(!$ini['status']) continue;
                    $data = request($id, leaderRaft::term());
                }
            }else{
                if(conf::$server[raft::$id]['status']==0){//3.未加入集群
                    $data = request(raft::$leader, askServ::join());
                    if($data && $data['status']==0){
                        conf::$server[raft::$id]['status']==1;
                        raft::$timeout = $data['data']['timeout'];

                    }
//                    var_dump(conf::$server);
                }elseif(conf::$server[raft::$id]['status']==3){//4.等待重启中

                }elseif(raft::$timeout>time()){//4.是否要变成竞选者

                }
//                var_dump(conf::$server);
//                var_dump(raft::$timeout);
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
            if(raft::$id!==raft::$leader) error(1001);
            if(raft::$leader!=$lid) error(41);
            if(conf::$server[raft::$leader]['host']!= $lhost) error(42);
            if(conf::$server[raft::$leader]['port']!= $lport) error(43);
            if(conf::$server[raft::$leader]['pass']!= $lpass) error(44);
            if(in_array(conf::$server[$cid]['status'],[1,3])) error(1002);
            conf::$server[$cid]['host'] = $chost;
            conf::$server[$cid]['port'] = $cport;
            conf::$server[$cid]['pass'] = $cpass;
            conf::$server[$cid]['status'] = 1;
            return strtotime('+6 s');//追加有效期
        }
        //leader信息
        public static function leader(){
            return raft::$leader;
        }
        //登陆
        public static function sign($fd, $uid, $sid, $shost, $sport, $spass){
            if($sid!=raft::$id) error(41);
            if(conf::$server[raft::$id]['host']!=$shost) error(42);
            if(conf::$server[raft::$id]['port']!=$sport) error(43);
            if(conf::$server[raft::$id]['pass']!=$spass) error(44);
            $session = session($fd, 'server');
            $rs = dim::$mem->hset($uid, 'session', $session);
            return $session;
        }
    }
<?php
    /**
     * app.serv.php
     * 服务应用
     * say
     * 2018-04-28
     */
    class appServ{
        //服务自检
        public static function check(){
            $role = raft::role();
            $raft_id = raft::id();
            $leader_id = raft::leader();
            $timeout = raft::timeout();
            //var_dump($role, $raft_id, $leader_id, raft::timeout(), '=================');
            switch($role){
                case 1://leader
                    conf::set($raft_id, 'status', 1);
                    foreach(conf::lists() as $id => $ini){
                        if($id==$raft_id) continue;
                        if(!$ini['status']) continue;
                        $data = request($id, askRaft::term());
                        if(!$data) conf::set($id, 'status', 2);
                        if($data['code']==1002) conf::set($id, 'status', 3);
                        if($data['status']==0) conf::set($id, 'status', 1);
                    }
                    break;
                case 2://follower
                    $info = conf::lists($raft_id);
                    if($info['status']==0){//未加入集群
                        $data = request($leader_id, askServ::join());
                        if($data && $data['status']==0){
                            conf::set($raft_id, 'status', 1);
                            conf::set($raft_id, 'timeout', $data['data']['timeout']);
                        }
                    }elseif($info['status']==3){//等待重启中

                    }elseif($timeout && $timeout<raft::time()){//是否要变成竞选者
                        raft::set('role', 3);
                    }
                    break;
                case 3://candidate
                    $count = 0;//集群数量
                    $vote = 1;//票数
                    $lists = conf::lists();
                    foreach($lists as $id => $ini){
                        if(in_array($ini['status'], [1])) $count++;
                        if($id==$raft_id) continue;
                        if(!$ini['status']) continue;
                        $data = request($id, askRaft::vote());
                        if($data && $data['status']==0) $vote++;
                    }
                    //var_dump('得票数'.$vote.'，需要票数'. ($count-1)/2);
                    if($vote > ($count-1)/2){//升级为leader
                        raft::set('role', 1);
                        raft::set('leader', $raft_id);
                        raft::set('term', raft::term()+1);
                        foreach($lists as $id => $ini){
                            if($id==$raft_id) continue;
                            $data = request($id, askRaft::succ());
                            //var_dump($data);
                        }
                    }
                    break;
            }
            return true;
        }
        //服务状态
        public static function status(){
            //TODO:返回服务器的状态，细节内容需要根据业务思考，一般包含集群信息、本机信息
            return dim::$server->stats();
        }
        //服务数据落地
        public static function backup(){
            $lists = conf::lists();
            foreach($lists as $id => $ini){
                foreach($ini as $k => $v){
                    if($k=='status'){
                        $ini[$k] = 0;
                        break;
                    }
                }
                ini_write('server', $id, $ini);
            }
        }
        //服务代码更新
        public static function update($cid){
            $info = askServ::diff();
            $client = new swoole_client(SWOOLE_SOCK_TCP);
            $rs = @$client->connect(conf::$server[$cid]['host'], conf::$server[$cid]['port']);
            if(!$rs) return false;
            //接受链接uid
            $str = $client->recv();
            $data = json_decode($str, 1);
            if(!$data) return false;
            if($data['status']!=0) return false;
            $info['uid'] = $data['data']['uid'];
            //注册
            $rs = $client->send(json_encode(askServ::sign($info['uid'], $cid)));
            $str = $client->recv();
            $data = json_decode($str, 1);
            if(!$data) return false;
            if($data['status']!=0) return false;
            $info['session'] = $data['data']['session'];
            var_dump($info);
            //*发送请求
            $rs = $client->send(json_encode($info));
            if(!$rs) return false;
            var_dump($rs);
            $str = $client->recv();
            $data = json_decode($str, 1);
            //TODO：文件发送与接受，并落地存储，存在一些细节需要处理
            var_dump($str, $data);
            if(!$data) return false;
            if($data['status']!=0) return false;
            var_dump($data);
            $str = $client->recv();
            var_dump($str);
            var_dump($data);
        }
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
            if(in_array($client['status'],[1,3])) error(1002);
            conf::set($cid, 'host', $chost);
            conf::set($cid, 'port', $cport);
            conf::set($cid, 'pass', $cpass);
            conf::set($cid, 'status', 1);

            dim::$server->task(askServ::update($cid));
            return raft::time(6000);//追加有效期
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
        //比对
        public static function diff($fd, $lists){
            $data = [];
            $local_lists = update_lists();
            foreach($local_lists as $lk => $lv){
                foreach($lists as $k => $v){
                    if($k==$lk){
                        if($lv==$v){
                            unset($local_lists[$lk]);
                        }
                        unset($lists[$k]);
                        break;
                    }
                }
            }
            if($local_lists){
                foreach($local_lists as $k => $v){
                    $local_lists[$k] = filesize(ROOT.$k);
                }
            }
            $data['add'] = $local_lists;
            $data['del'] = $lists;
            dim::$server->send($fd, json_encode($data));
            foreach($data['add'] as $k => $v){
                dim::$server->sendfile($fd, ROOT.$k);
            }
            var_dump('比对文件区别', $data);
        }
    }
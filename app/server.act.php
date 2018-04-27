<?php
    /**
     * server.cls.php
     * 服务操作类
     * say
     * 2018-03-28
     */
    class serverAct extends act implements actInterface{
        //登陆
        public function sign(){
            $leader_pass = $this->get('leader_pass');
            $id = $this->get('id');
            $host = $this->get('host');
            $port = $this->get('port');
            $pass = $this->get('pass');
            if(raft::$servers[raft::$leader]['pass']!=$leader_pass) error(42);
            if(raft::$servers[$id]['status']==1) error(43);
            foreach(raft::$servers as $k => $v){
                if($k==$id) continue;
                if($v['status']!=1) continue;
                if($v['host']==$host && $v['port']==$port) error(43);
            }
            if(raft::$current==raft::$leader){
                raft::$servers[$id]['host'] = $host;
                raft::$servers[$id]['port'] = $port;
                raft::$servers[$id]['pass'] = $pass;
                raft::$servers[$id]['status'] = 1;
            }
            $session = session($this->fd, 'server');
            $rs = dim::$mem->hset($this->get('uid'), 'session', $session);

            var_dump($this->par, $rs, $this->get('uid'), $session,'=========');
            $this->data['session'] = $session;
        }

        public function msg(){
            // TODO: Implement msg() method.
        }

        public function quit(){
            // TODO: Implement quit() method.
        }

        public function leader(){
            $this->data['leader'] = raft::$leader;
        }
        //状态
        public function status(){
            dim::$server->task(task::status());
        }
    }
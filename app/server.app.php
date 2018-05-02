<?php
    /**
     * server.app.php
     * 服务操作类
     * say
     * 2018-03-28
     */
    class serverApp extends app implements appInterface{
        //登陆
        public function sign(){
            $uid = $this->get('uid');
            $leader_pass = $this->get('leader_pass');
            $id = $this->get('id');
            $host = $this->get('host');
            $port = $this->get('port');
            $pass = $this->get('pass');
            $this->data['session'] = appServ::sign($this->fd, $uid, $leader_pass, $id, $host, $port, $pass);
        }

        public function msg(){
            // TODO: Implement msg() method.
        }

        public function quit(){
            // TODO: Implement quit() method.
        }

        public function leader(){
            $this->data['leader'] = appServ::leader();
        }
        //状态
        public function status(){
            dim::$server->task(askServ::status());
        }
    }
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
            $sid = $this->get('sid');
            $shost = $this->get('shost');
            $sport = $this->get('sport');
            $spass = $this->get('spass');
            $this->data['session'] = appServ::sign($this->fd, $uid, $sid, $shost, $sport, $spass);
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
        //自检
        public function check(){
            dim::$server->task(askServ::check());
        }
        //状态
        public function status(){
            $this->data = appServ::status();
        }
        //加入集群
        public function join(){
            $cid = $this->get('cid');
            $chost = $this->get('chost');
            $cport = $this->get('cport');
            $cpass = $this->get('cpass');
            $lid = $this->get('lid');
            $lhost = $this->get('lhost');
            $lport = $this->get('lport');
            $lpass = $this->get('lpass');
            $this->data['timeout'] = appServ::join($cid, $chost, $cport, $cpass, $lid, $lhost, $lport, $lpass);
        }
        //任期
        public function term(){
            $term = $this->get('term');
            $conf = $this->get('conf');
            appRaft::term($term, $conf);
        }
        //投票
        public function vote(){
            $term = $this->get('term');
            $cid = $this->get('cid');
            appRaft::vote($term, $cid);
        }
        //选举成功
        public function succ(){
            $lid = $this->get('lid');
            $term = $this->get('term');
            $conf = $this->get('conf');
            appRaft::succ($lid, $term, $conf);
        }
        //配置备份
        public function backup(){
            appServ::backup();
        }
    }
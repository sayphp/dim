<?php
    /*
     * 管理员应用类
     * say
     * 2018-05-09
     */
    class adminApp extends app implements appInterface{

        public $method = 0;

        public function sign(){
            $uid = uid($this->fd);
            $sess = $this->get('sess');
            $users = [
                '1' => 'harry',
                '2' => 'pinky',
                '3' => 'tree',
            ];
            if(!isset($users[$sess])) error(2003);
            dim::$mem->hset($uid, 'session', md5($sess));
            dim::$mem->sadd('user::'.$sess, $uid);
            $data['session'] = md5($sess);
            $data['fd'] = $this->fd;
            $data['uid'] = $uid;
            $data['name'] = $users[$sess];
            $recv = encode(json_encode(appServ::reply('admin', 'sign', $data)));
            dim::$server->send($this->fd, $recv);
        }

        public function msg(){
            $uid = uid($this->fd);
            $to = $this->get('to');
            $msg = $this->get('msg');
            $lists = dim::$mem->smem('user::'.$to);
            if($lists){
                var_dump($lists);
                foreach($lists as $v){
                    $fd = dim::$mem->hget($v, 'fd');
                    var_dump($this->fd, $fd,'==================');
                    $recv = encode(json_encode(appServ::send('admin', 'msg', $msg)));
                    dim::$server->send($fd, $recv);
                }

            }
        }

        public function quit(){
            // TODO: Implement quit() method.
        }
    }
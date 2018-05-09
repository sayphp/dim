<?php
    /*
     * 管理员应用类
     * say
     * 2018-05-09
     */
    class adminApp extends app implements appInterface{

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
            $this->data['session'] = md5($sess);
            $this->data['fd'] = $this->fd;
            $this->data['uid'] = $uid;
            $this->data['name'] = $users[$sess];
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
                    $data = [
                        'act' => 'admin',
                        'method' => 'msg',
                        'data' => [
                            'msg' => $msg
                        ],
                    ];
                    var_dump($this->fd, $fd,'==================');
                    $recv = encode(json_encode($data));
                    dim::$server->send($fd, $recv);
                }

            }
        }

        public function quit(){
            // TODO: Implement quit() method.
        }
    }
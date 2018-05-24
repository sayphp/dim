<?php
    /**
     * 知识问答
     * say
     * 2018-05-24
     */
    class zhishiApp extends app implements appInterface{

        public $method = 0;

        public function sign(){
            $uid = uid($this->fd);
            $member_id = $this->get('member_id');
            $session = md5('zhishi'.$member_id);
            dim::$mem->hset($uid, 'session', $session);
            dim::$mem->sadd('zhishi::'.$session, $uid);
            $data = [
                'session' => $session,
            ];
            $recv = encode(json_encode(appServ::reply('zhishi', 'sign', $data)));
            dim::$server->send($this->fd, $recv);
        }

        public function msg(){

        }

        public function mass(){
            $msg = $this->get('msg');
            dim::$server->task(askServ::mass('zhishi', $msg));
        }

        public function quit(){
            // TODO: Implement quit() method.
        }
    }
<?php
    /**
     * Created by PhpStorm.
     * User: say
     * Date: 18-3-7
     * Time: 下午5:53
     */
    class userAct extends act implements actInterface{

        public function sign(){
            $d = [
                'hehehe' => '123',
                'liuxiao' => '123'
            ];
            $account = $this->get('account');
            $password = $this->get('password');

            if(isset($d[$account]) && $d[$account]==$password){
                $session = md5($account.$password.time());
                $this->server->mem->hset(dim::uid($this->fd), 'session', $session);
                $data = [
                    'status' => 0,
                    'error' => 'ok',
                    'data' => [
                        'session' => $session,
                    ],
                ];
            }else{
                $data = [
                    'status' => 2,
                    'error' => '错误账号密码',
                ];
            }

            $this->server->send($this->fd, json_encode($data));
        }

        public function msg(){
            // TODO: Implement msg() method.
        }

        public function quit(){
            // TODO: Implement quit() method.
        }

        public function test(){
            $data = [
                'status' => 0,
                'error' => 'ok',
                'data' => [
                    'example' => 'panda kill!!!!',
                ],
            ];
            $this->server->send($this->fd, json_encode($data));
        }
    }
<?php
    /**
     * 服务器任务类
     * serverTask.task.php
     * say
     * 2018-04-27
     */
    class serverTask extends task{
        //运行
        public function run(){
            while(true){
                $raft_id = raft::id();
                //1. 服务状态检查
                request($raft_id, askServ::check());
                //2. 数据落地服务
                if(in_array(conf::$system['mode'], [1, 2])) request($raft_id, askServ::backup());
                //3. 自定义任务处理
//                echo 'custom task'.time().PHP_EOL;
                sleep(5);
            }
        }
        //服务器状态检查
        public function check(){
            $status = appServ::check();
        }
        //更新
        public function update(){
            $cid = $this->get('cid');
            appServ::update($cid);
        }
        //群发
        public function mass(){
            var_dump('================', '投递群发任务');
            $target = $this->get('target');
            $msg = $this->get('msg');
            switch($target){
                case 'zhishi':
//                    foreach(dim::$server->connections as $fd){
//                        dim::$server->send($fd, encode(json_encode(appServ::send($target, 'msg', $msg))));
//                    }
                    $user_keys = dim::$mem->keys('zhishi::*');
                    foreach($user_keys as $key){
                        $u_lists = dim::$mem->smem($key);
                        foreach($u_lists as $uid){
                            $fd = dim::$mem->hget($uid, 'fd');
                            var_dump($fd);
                            var_dump('======投递：'.$fd);
                            dim::$server->send($fd, encode(json_encode(appServ::send($target, 'msg', $msg))));
                        }
                    }
                    break;
                default:
                    error(999);
            }
        }
    }
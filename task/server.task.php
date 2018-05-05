<?php
    /**
     * 服务器任务类
     * serverTask.task.php
     * say
     * 2018-04-27
     */
    class serverTask{
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
    }
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
                //1. 服务状态检查
                request(raft::$id, askServ::status());
//                echo 'server status check'.time().PHP_EOL;
                //2. 分发数据落地服务
//                echo 'publish task'.time().PHP_EOL;
                //3. 自定义任务处理
//                echo 'custom task'.time().PHP_EOL;
                sleep(5);
            }
        }
        //服务器状态检查
        public function status(){
            $status = appServ::status();
            var_dump(raft::$id, raft::$leader);
        }
        //服务器数据落地
        public function land(){

        }
    }
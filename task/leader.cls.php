<?php
    /**
     * leader.cls.php
     * 领导者
     * say
     * 2018-04-07
     */
    class leader{
        //领导者任务处理
        public static function deal(){
            $par = [
                'act' => 'leader',
                'method' => 'deal',
            ];
            self::$server->task($par);
            task::$leader = time();
        }
    }
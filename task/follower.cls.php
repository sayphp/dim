<?php
    /**
     * follower.cls.php
     * 跟随者
     * say
     * 2018-04-07
     */
    class follower{
        //跟随者任务处理
        public static function deal(){
            $par = [
                'act' => 'follower',
                'method' => 'deal',
            ];
            self::$server->task($par);
            task::$follower = time();
        }
    }
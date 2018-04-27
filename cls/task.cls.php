<?php
    /**
     * task.cls.php
     * 定时器
     * say
     * 2018-04-02
     */
    class task{

        public static $leader = 0;//领导者任务状态

        public static $follower = 0;//跟随者任务状态

        public static $task = [];//任务列表
        /*
         * $task = [
         *   [
         *      'act' => 'test',
         *      'method => 'test',
         *   ],
         * ];
         */
        //状态检查
        public static function status(){
            $par = [
                'act' => 'server',
                'method' => 'status',
            ];
            return $par;
        }
    }
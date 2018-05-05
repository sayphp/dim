<?php
    /**
     * raft请求类
     * say
     * 2018-05-03
     */
    class askRaft{

        //*任期
        public static function term(){
            return [
                'act' => 'server',
                'method' => 'term',
                'term' => raft::term(),
            ];
        }
    }
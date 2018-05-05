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
                'conf' => conf::lists(),
            ];
        }
        //*投票
        public static function vote(){
            return [
                'act' => 'server',
                'method' => 'vote',
                'cid' => raft::id(),
                'term' => raft::term()+1,
            ];
        }
        //*选举成功
        public static function succ(){
            return [
                'act' => 'server',
                'method' => 'succ',
                'lid' => raft::id(),
                'term' => raft::term(),
                'conf' => conf::lists(),
            ];
        }
    }
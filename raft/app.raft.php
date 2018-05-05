<?php
    /**
     * app.raft.php
     * raft应用
     * say
     * 2018-05-03
     */
    class appRaft{
        
        //*任期
        public static function term($term){
            if(raft::term()>$term) error(1001);
            raft::set('term', $term);
            raft::set('timeout', time());
        }
    }
<?php
    /**
     * app.raft.php
     * raft应用
     * say
     * 2018-05-03
     */
    class appRaft{

        public static function term($term){
            if(raft::term()>$term) error(1001);
            raft::set('term', $term);
            raft::set('timeout', time());
            var_dump('follower有效期延期');
        }
    }
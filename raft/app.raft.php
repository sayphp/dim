<?php
    /**
     * app.raft.php
     * raft应用
     * say
     * 2018-05-03
     */
    class appRaft{

        //*任期
        public static function term($term, $conf){
            $current_term = raft::term();
            if($current_term>$term) error(1001);
            $info = conf::lists(raft::id());
            if($info['status']==3) error(1003);
            if($current_term<$term){
                raft::set('term', $term);
                raft::set('vote', 0);
            }
            raft::set('timeout', raft::time(6000));
            foreach($conf as $k => $ini){
                conf::$server->set($k, $ini);//同步配置
            }
            //var_dump($term,'续一秒'.raft::timeout());
        }
        //*投票
        public static function vote($term, $cid){
            if(raft::term()>=$term) error(1011, '任期：'.$term.'----发起方：'.$cid);
            if(raft::vote()) error(1012);
            raft::set('vote', $cid);
            var_dump('投票给：'.$cid);
        }
        //*选举成功
        public static function succ($lid, $term, $conf){
            raft::set('leader', $lid);
            raft::set('term', $term);
            raft::set('role', 2);
            raft::set('vote', 0);
            raft::set('timeout', raft::time(6000));
            foreach($conf as $k => $ini){
                conf::$server->set($k, $ini);//同步配置
            }
        }
    }
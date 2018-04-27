<?php
    /**
     * raft.cls.php
     * raft共识算法类
     * say
     * 2018-03-28
     */
    class raft{

        public static $ini = [];//配置

        public static $is_task = 0;//任务执行中

        public static $mode;//模式

        public static $id;//服务索引

        public static $role;//角色 1.leader 2.follower 3.candidate

        public static $timeout;//超时

        public static $leader;//leader服务器索引

        public static $term;//当前任期

        public static $vote;//投票对象

        public static $logs;//日志

        public static $commit;//提交索引

        public static $current;//当前服务器

        public static $servers;//所有集群

        //初始化
        public static function init(){
            $ini_lists = glob(ROOT.'conf/server/*.ini');
            foreach($ini_lists as $k => $file){
                preg_match("/(\d+)\.ini/s", $file, $matches);
                $id = $matches[1];
                self::$servers[$id] = parse_ini_file($file);
                self::$servers[$id]['status'] = 0;
            }
            $ini_lists = glob(ROOT.'conf/sys/*.ini');
            foreach($ini_lists as $k => $file){
                self::$ini = array_merge(self::$ini, parse_ini_file($file));
            }
            self::$mode = self::$ini['mode'];
            self::$timeout = self::$ini['timeout'];
        }

        //加入集群
        public static function leader(){
            //1.检查配置
            switch(self::$mode){
                case 1://外网
                    break;
                case 2://内网
                    break;
                case 3://本地
                    $free_lists = [];
                    foreach(self::$servers as $id => $ini){
                        $data = request($id, server::leader());
                        if($data){
                            if($data && $data['status']==0){
                                self::$leader = $data['data']['leader'];
                            }
                        }else{
                            $free_lists[$id] = $ini;
                        }
                    }
                    if(!$free_lists) error(11);//所有服务器均已运行
                    if(!self::$leader){
                        self::$leader = key($free_lists);
                    }
                    self::$id = key($free_lists);
                    self::$current = current($free_lists);
                    break;
            }
        }
    }
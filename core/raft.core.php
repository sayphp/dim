<?php
    /**
     * raft.core.php
     * raft共识算法类
     * say
     * 2018-03-28
     */
    class raft{

        public static $raft;//配置

//        public static $id;//服务索引

//        public static $role;//角色 1.leader 2.follower 3.candidate

//        public static $timeout;//超时

//        public static $leader;//leader服务器索引

//        public static $term;//当前任期

//        public static $vote;//投票对象

//        public static $logs;//日志

//        public static $commit;//提交索引


        //初始化
        public static function init(){
            self::$raft = new swoole_table(1);
            self::$raft->column('id', swoole_table::TYPE_INT, 2);//当前服务器编号
            self::$raft->column('leader', swoole_table::TYPE_INT, 2);//当前服务器编号
            self::$raft->column('role', swoole_table::TYPE_INT, 1);//当前服务器角色
            self::$raft->column('timeout', swoole_table::TYPE_INT, 4);//超时时间戳
            self::$raft->column('term', swoole_table::TYPE_INT, 4);//任期
            self::$raft->column('vote', swoole_table::TYPE_INT, 2);//投票对象
            self::$raft->create();
            //1.检查配置
            switch(conf::$system['mode']){
                case 1://外网
                    break;
                case 2://内网
                    break;
                case 3://本地
                    $id = 0;
                    $leader_id = 0;
                    $free_lists = [];
                    foreach(conf::$server as $id => $ini){
                        $data = request($id, askServ::leader());
                        if($data){
                            if($data && $data['status']==0){
                                $leader_id = $data['data']['leader'];
                            }
                        }else{
                            $free_lists[$id] = $ini;
                        }
                    }
                    if(!$free_lists) error(31);//所有服务器均已运行
                    if(!$leader_id){
                        $leader_id = key($free_lists);
                    }
                    $id = key($free_lists);
                    $role = $id==$leader_id?1:2;
                    $data = [
                        'id' => $id,
                        'leader' => $leader_id,
                        'role' => $role,
                        'timeout' => 0,
                        'vote' => 0,
                    ];
                    self::$raft->set(1, $data);
                    break;
                default:
                    error(101);
            }
        }
        //获取当前服务索引
        public static function id(){
            return self::$raft->get(1, 'id');
        }
        //获取leader服务索引
        public static function leader(){
            return self::$raft->get(1, 'leader');
        }
        //获取当前服务期角色
        public static function role(){
            return self::$raft->get(1, 'role');
        }
        //获取服务有效期
        public static function timeout(){
            return self::$raft->get(1, 'timeout');
        }
        //获取任期
        public static function term(){
            return self::$raft->get(1, 'term');
        }
        //获取投票目标
        public static function vote(){
            return self::$raft->get(1, 'vote');
        }
        //设置值
        public static function set($key, $value){
            $data = self::$raft->get(1);
            $data[$key] = $value;
            self::$raft->set(1, $data);
        }
    }
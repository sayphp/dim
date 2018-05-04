<?php
    /*
     * conf.core.php
     * 配置类
     * say
     * 2018-05-01
     */
    class conf{

        public static $system;//系统

        public static $code;//错误码

        public static $server;//服务

        public static $redis;//redis

        public static $task;//任务

        //初始化
        public static function init(){
            //1.加载系统配置
            self::system();
            //2.加载错误码
            self::code();
            //3.加载服务集群配置
            self::server();
            //4.加载redis配置
            self::redis();
            //5.加载任务配置
            self::task();
        }

        //系统
        public static function system(){
            $data = self::load('system');
            foreach($data as $k => $v){
                foreach($v as $vk => $vv){
                    self::$system[$vk] = $vv;
                }
            }
        }

        //错误码加载
        public static function code(){
            $data = self::load('code');
            foreach($data as $k => $v){
                foreach($v as $vk => $vv){
                    self::$code[$vk] = $vv;
                }
            }
            self::$code[999] = '未知错误';
        }

        //服务
        public static function server(){
            $data = self::load('server');
            self::$server = new swoole_table(1024);
            self::$server->column('host', swoole_table::TYPE_STRING, 15);//服务地址
            self::$server->column('port', swoole_table::TYPE_INT, 2);//端口
            self::$server->column('pass', swoole_table::TYPE_STRING, 16);//密码
            self::$server->column('status', swoole_table::TYPE_INT, 1);//状态
            self::$server->column('work_num', swoole_table::TYPE_INT, 2);//worker数量
            self::$server->column('max_request', swoole_table::TYPE_INT, 2);//最大请求数
            self::$server->column('max_conn', swoole_table::TYPE_INT, 2);//最大连接数
            self::$server->column('task_worker_num', swoole_table::TYPE_INT, 1);//tasker数量
            self::$server->create();
            foreach($data as $k => $v){
                self::$server->set($k, $v);
            }
        }

        //redis
        public static function redis(){
            self::$redis = self::load('redis');
        }

        //任务
        public static function task(){
            self::$task = self::load('redis');
        }

        //载入配置
        public static function load($name='default'){
            $preg = ROOT.'conf/'.$name.'/*.ini';
            $ini_lists = glob($preg);
            $data = [];
            foreach($ini_lists as $k => $file){
                preg_match("/(\w+)\.ini/s", $file, $matches);
                $id = $matches[1];
                $data[$id] = parse_ini_file($file);
            }
            return $data;
        }

        //*错误码内容
        public static function error($code){
            return isset(self::$code[$code])?self::$code[$code]:'未知错误';
        }

        //*服务列表
        public static function lists($key=0){
            if($key) return self::$server->get($key);
            $count = self::$server->count();
            $data = [];
            for($i=1;$i<=$count;$i++){
                $data[$i] = self::$server->get($i);
            }
            return $data;
        }
        //设置
        public static function set($id, $key, $value){
            $data = self::$server->get($id);
            $data[$key] = $value;
            self::$server->set($id, $data);
        }
    }
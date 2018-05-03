<?php
    /**
     * dim.core.php
     * 分布式即时通讯服务类
     * say
     * 2018-03-28
     */
    class dim{

        public static $lock;//锁

        public static $server;//服务

        public static $mem;//缓存

        protected static $config;//配置

        protected static $raft;//共识信息

        //初始化
        public static function init(){
            try{
                //1. 获取服务配置
                conf::init();
                //2. 获取raft配置
                raft::init();
                //3. 设置互斥锁
                self::$lock = new swoole_lock(SWOOLE_MUTEX);
                //4. 初始化swoole_server
                self::$server = new swoole_server(raft::$current['host'], raft::$current['port']);
            }catch (Exception $e){
                echo $e->getCode().'::'.$e->getMessage().PHP_EOL;
                exit();
            }
        }

        //Start!
        public static function start(){
            self::$server->set(raft::$current);//配置设定
            self::$server->on('Start','dim::onStart');
            self::$server->on('Shutdown', 'dim::onShutdown');
            self::$server->on('WorkerStart', 'dim::onWorkerStart');
            self::$server->on('Connect', 'dim::onConnect');
            self::$server->on('Receive', 'dim::onReceive');
            self::$server->on('Close', 'dim::onClose');
            self::$server->on('Task', 'dim::onTask');
            self::$server->on('Finish', 'dim::onFinish');
            self::$server->start();
        }
        //*服务开启
        public static function onStart($server){

        }
        //*服务关闭
        public static function onShutdown($server){
            var_dump('服务关闭');
        }
        //*工人进程开启
        public static function onWorkerStart($server, $worker_id){
            self::$mem = mem::get_instance(1);
            if(!self::$server->taskworker){
                if(self::$lock->trylock()){
                    $par = [
                        'act' => 'server',
                        'method' => 'run',
                    ];
                    dim::$server->task($par);
                }
            }
        }
        //*连接
        public static function onConnect($server, $fd, $reactor_id){
            $data = [
                'status' => 0,
                'code' => 0,
                'error'=> 'ok',
                'data' => [
                    'uid' => uid($fd),
                ],
            ];
            self::$mem->hset($data['data']['uid'], 'fd', $fd);
            $server->send($fd, json_encode($data));
            echo 'Connect'.PHP_EOL;
        }
        //*收到信息
        public static function onReceive($server, $fd, $reactor_id, $data){
            try{
                $data = json_decode($data, 1);
                if(!$data) close($fd, 301);
                if(!isset($data['uid'])) close($fd, 201);
                if($data['uid'] != uid($fd)) close($fd, 221);
                if(!isset($data['act'])) close($fd, 202);
                if(!isset($data['method'])) close($fd, 203);
                $file = ROOT.'app/'.$data['act'].'.app.php';
                if(!file_exists($file)) close($fd, 11);
                require_once $file;
                if(!in_array($data['method'], ['sign'])){
                    if(!isset($data['session'])) close($fd, 204);
                    $session = self::$mem->hget($data['uid'], 'session');
                    if(!$session) close($fd, 224);
                    if($session!=$data['session']) close($fd, 224);
                }
                $class_name = $data['act'].'App';
                if(!class_exists($class_name)) close($fd, 12);
                $cls = new $class_name($fd, $data);
                if(!method_exists($cls, $data['method'])) close($fd, 13);
                $cls->{$data['method']}();
                $data = [
                    'status' => 0,
                    'code' => 0,
                    'error'=> 'ok',
                ];
                if($cls->data!==null) $data['data'] = $cls->data;
                switch($cls->method){
                    case 1://回复
                        self::$server->send($fd, json_encode($data));
                        break;
                    case 2://转发
                        self::$server->send($fd, json_encode($data));
                        break;
                    case 3://群发
                        self::$server->send($fd, json_encode($data));
                        break;
                }
                echo 'Receive'.PHP_EOL;
            }catch (Exception $e){
                $data = [
                    'status' => 2,
                    'code' => $e->getCode(),
                    'error' => $e->getMessage(),
                ];
                self::$server->send($fd, json_encode($data));
                echo $e->getCode().'::'.$e->getMessage().PHP_EOL;
            }
        }
        //*链接断开
        public static function onClose($server, $fd, $reactor_id){
            self::$mem->del(uid($fd));
            var_dump('断开连接');
        }
        //*任务
        public static function onTask($server, $task_id, $src_worker_id, $data){
            try{
                if(!isset($data['act'])) error(202);
                if(!isset($data['method'])) error(203);
                $file = ROOT.'task/'.$data['act'].'.task.php';
                if(!file_exists($file)) error(11);
                require_once $file;
                $class_name = $data['act'].'Task';
                $cls = new $class_name($data);
                $rs = $cls->{$data['method']}();
                if($rs) self::$server->finish('ok');
            }catch (Exception $e){
                echo $e->getCode().'::'.$e->getMessage().PHP_EOL;
            }
        }
        //*任务完成
        public static function onFinish($server, $task_id, $data){
            echo $task_id.'::完成'.PHP_EOL;
        }
        //*定时器
        public static function onTimer($timer_id){}
    }
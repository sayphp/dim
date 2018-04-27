<?php
    /**
     * dim.cls.php
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
            //1. 载入目录配置
            self::load();
            //2. 加载错误码
            code::init();
            //3. 获取服务配置
            raft::init();
            //4. 找寻当前配置
            raft::leader();
            //5. 设置互斥锁
            self::$lock = new swoole_lock(SWOOLE_MUTEX);
            //6. 初始化swoole_server
            self::$server = new swoole_server(raft::$current['host'], raft::$current['port']);
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

        //关闭连接
        public static function close($fd, $code=999){
            self::$server->close($fd);
            error($code);
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
                var_dump($data);
                if(!$data) self::close($fd, 31);
                if(!isset($data['uid'])) self::close($fd, 32);
                if($data['uid'] != uid($fd)) self::close($fd, 35);
                if(!isset($data['act'])) self::close($fd, 33);
                if(!isset($data['method'])) self::close($fd, 34);
                $file = ROOT.'act/'.$data['act'].'.act.php';
                if(!file_exists($file)) self::close($fd, 36);
                require_once $file;
                if(!in_array($data['method'], ['sign'])){
                    if(!isset($data['session'])) self::close($fd, 37);
                    $session = self::$mem->hget($data['uid'], 'session');
                    if(!$session) self::close($fd, 37);
                    if($session!=$data['session']) self::close($fd, 37);
                }
                $class_name = $data['act'].'Act';
                if(!class_exists($class_name)) self::close($fd, 11);
                $cls = new $class_name($server, $fd, $data);
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
                if(!isset($data['act'])) error(33);
                if(!isset($data['method'])) error(34);
                switch($data['act']){
                    default:
                        $file = ROOT.'task/'.$data['act'].'.task.php';
                        if(!file_exists($file)) error(38);
                        require_once $file;
                        $class_name = $data['act'].'Task';
                        $cls = new $class_name($data);
                        $rs = $cls->{$data['method']}();
                        if($rs) self::$server->finish('ok');
                }
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
        //加载文件
        public static function load(){
            $cls_lists = glob(ROOT.'cls/*.cls.php');
            foreach ($cls_lists as $file){
                if($file==ROOT.'cls/dim.cls.php') continue;
                require $file;
            }
            require ROOT.'inc/function.php';
        }
    }
<?php
    /**
     * redis.core.php
     * 共享内存类
     */
    class mem{

        public static $instance;//单例

        private $redis;//redis对象

        public static $name;//对象名

        private static $config;//配置

        public static function get_instance($name=1){
            self::name($name);
            if(!isset(self::$instance[self::$name])){
                return self::$instance[self::$name] = new mem();
            }else{
                return self::$instance[self::$name];
            }
            return false;
        }
        public function __construct(){
            $this->redis = new Redis();
            $rs = $this->redis->connect(self::$config['host'], self::$config['port'], self::$config['time']);
            if($rs){
                return $this->redis->auth(self::$config['pass']);
            }else{
                return false;
            }
        }

        //设定当前使用的单例
        public static function name($name=1){
            $file = ROOT.'conf/redis/'.$name.'.ini';
            if(!file_exists($file)) error(12);
            self::$config = parse_ini_file($file);
            return true;
        }

        //hash设定
        public function hset($key, $field, $value){
            if(isset($key) && isset($field) && isset($value)) return $this->redis->hSet($key, $field, $value);
            return false;
        }
        //hash获取
        public function hget($key, $field){
            if(isset($key) && isset($field)) return $this->redis->hGet($key, $field);
            return false;
        }
        //删除健
        public function del($key){
            if(isset($key)) return $this->redis->del($key);
            return false;
        }
    }
<?php
    /**
     * 基类,接口
     * User: say
     * Date: 18-3-14
     * Time: 上午11:00
     */
    class app{

        public $method = 1;//消息模式

        protected $fd;

        protected $par;

        public $data;

        public function __construct($fd, $par){
            $this->fd = $fd;
            $this->par = $par;
        }

        public function __call($method, $arg){
            close($this->fd, 13);
        }

        public function get($key){
            return isset($this->par[$key])?$this->par[$key]:false;
        }

    }
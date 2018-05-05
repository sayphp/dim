<?php
    /**
     * 任务基类
     * say
     * 2018-05-05
     */
    class task{

        protected $par;

        public function __construct($par){
            $this->par = $par;
        }

        public function __call($method, $arg){
            close($this->fd, 13);
        }

        public function get($key){
            return isset($this->par[$key])?$this->par[$key]:false;
        }

    }
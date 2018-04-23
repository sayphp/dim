<?php
    /**
     * 错误码
     */
    class code{
        //*错误码
        public static $code = [];

        //初始化
        public static function init(){
            //*1.读取错误码文件
            $lists = glob(ROOT.'conf/code/*.ini');
            //*2.赋值
            foreach($lists as $file){
                $codes = parse_ini_file($file);
                foreach($codes as $k => $v){
                    self::$code[$k] = $v;
                }
            }
        }

        //*错误码内容
        public static function error($code){
            return isset(self::$code[$code])?self::$code[$code]:'未知错误';
        }
    }
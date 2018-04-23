<?php
    /**
     * dim.php
     * 分布式即时通讯服务脚本
     * Say
     * 2018-03-28
     */
    define('ROOT', __DIR__.DIRECTORY_SEPARATOR);
    require 'cls/dim.cls.php';

    dim::init();
    dim::start();
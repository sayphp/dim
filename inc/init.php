<?php
    /**
     * 初始化脚本
     */

    $conf_lists = glob(ROOT.'conf/*.conf.php');
    foreach($conf_lists as $file){
        require $file;
    }
    $cls_lists = glob(CLS.'*.cls.php');
    foreach ($cls_lists as $file){
        require $file;
    }
    require INC.'function.php';
<?php
    /**
     * 初始化脚本
     * init.php
     * say
     * 2018-04-28
     */
    $range = '{'.ROOT.'core/*.core.php,'.ROOT.'raft/*.raft.php,'.ROOT.'serv/*.serv.php,'.ROOT.'interface/*.interface.php}';
    $file_lists = glob($range, GLOB_BRACE);
    foreach ($file_lists as $file){
        require $file;
    }
    require ROOT.'/inc/function.php';
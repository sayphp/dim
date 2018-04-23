<?php
    /**
     * 公用方法
     */

    function error($code, $msg=null){
        $msg = $msg?$msg:code::error($code);
        throw new Exception($msg, $code);
    }

    function uid($fd){
        return md5(raft::$id.'_'.$fd);
    }
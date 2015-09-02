<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-24 09:55
 */

namespace Common\String;

abstract class Encode {
    static public function json($obj){
        return json_encode($obj, JSON_UNESCAPED_UNICODE);
    }
} 
<?php
/**
 * 
 * @package Comm/App
 * @Autor: fengqiang <fengqiang1@staff.sina.com.cn>
 * @Date: 2014-11-02 16:37
 */
class Comm_App {
    static public function hasLogin(){
        $session = Yaf_Session::getInstance();
        $username = $session->offsetGet('username');
        return !empty($username);
    }
} 
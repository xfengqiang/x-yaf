<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-23 14:17
 */



class TestController extends \Http\Controller\Cli{
    public function init(){
        parent::init();
    }
    
    public function runAction(){
        apps\cli\services\Test::testDao();
    }
} 
<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-24 15:34
 */
class IndexController extends \Http\Controller\Cli{
    public function init(){
        parent::init();
    }

    public function indexAction(){
        echo "Hello";
        //        \Common\Mysql\Query::runTest();
    }

    public function dbAction(){
        echo "db";
    }

    public function daoAction(){


    }
} 
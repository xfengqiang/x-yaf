<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-24 12:32
 */

namespace apps\cli\services;

use \Common\Mysql\DbCache;
use \Common\Mysql\Dao;
use \Common\Mysql\Query;
use \Common\Mysql\Db;
use apps\common\models\User;

class Test {
    public static function testDb() {
        $db = new Db('xtest');
        $db->beginTransaction();
        $db->fetchRows('user', 'id>?', [1]);
        $id = $db->insert('user', ['name'=>'abcd']);
        $db->rollback();
        var_dump($id);
    }
    public static function testDao(){
        $config = ['host'=>'127.0.0.1', 'port'=>3306, 'dbname'=>'xtest', 'user'=>'root', 'password'=>'z'];
        DbCache::RegisterDb('xtest', $config);
        $config = ['host'=>'127.0.0.1', 'port'=>3306, 'dbname'=>'test', 'user'=>'root', 'password'=>'z'];
        DbCache::RegisterDb('xtest', $config, false);
        
        $dao = Dao::daoByName('User');
        $ret = $dao->fetchByPk(['id'=>1]);
        var_dump($ret);
        $ret = $dao->fetchRows(Query::query()->addCond('id', 0, '>'));
        var_dump($ret);
        $ret = $dao->updateByPk(['id'=>1, 'create_time'=>'10']);
        var_dump($ret);
        $ret =  $dao->update(['create_time'=>10], Query::query()->addCond('id', 0, '>'));
        var_dump($ret);

        $ret = $dao->deleteByPk(['id'=>1]);
        var_dump($ret);


        $dao = Dao::daoWithDb('xtest');
        $ret = $dao->beginTransaction();
        var_dump($ret);
        $userModel = new User();
        $dao->setRecord($userModel);
        $ret =  $dao->update(['create_time'=>102], Query::query()->addCond('id', 0, '>'));
        var_dump($ret);
        $ret = $dao->rollback();
        var_dump($ret);
    }
} 
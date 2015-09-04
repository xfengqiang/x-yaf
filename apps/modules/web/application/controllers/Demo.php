<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-27 21:37
 */
use apps\web\services\Test;
use apps\common\models\User;
class DemoController extends \Http\Controller\Web {
    public function init(){
        parent::init();
        $this->disableView();
    }
    
    public function testAction(){
        $params = $this->params(["name", "age", "email", "phone","height", 'gender'], [], new User());
        var_dump($params);
    }
    
    public function testDbAction(){
        Test::testDao();
    }
    
    public function configAction(){
        $ret = \Common\Config::getAppConfig('app', 'common.site');
        echo json_encode($ret);
    }
} 
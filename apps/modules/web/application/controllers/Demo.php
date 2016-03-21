<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-27 21:37
 */
use apps\web\services\Test;
use apps\common\models\User;
class DemoController extends \Http\Controller\Base {
    public function init(){
        parent::init();
        
    }
    
    public function testAction(){
        $this->disableView();
        $params = $this->params(["name", "age", "email", "phone","height", 'gender'], [], new User());
        var_dump($params);
    }
    
    public function testDbAction(){
        $this->disableView();
        Test::testDao();
    }
    
    public function configAction(){
        $this->disableView();
        $ret = \Common\Config::getAppConfig('app', 'common.site');
        var_dump($ret);
        var_dump(\apps\common\App::getEnv());
    }

    /**
     * js 表单验证测试
     */
    public function formAction(){
        $params = array(
            'username'=>1,
            'desc'=>1,
            'email'=>1,
            'height'=>1,
            'age'=>1,
            'gender_enum'=>1,
            'gender'=>1,
            'password'=>1,
            'password_confirm'=>1,
            'accept_rule'=>1,
        );
        $testRules = new \apps\common\models\RuleTest();
        $jsRules = $this->getRuleJs($params, $testRules);
        $this->renderView(['js_config'=>$jsRules]);
    }

    /**
     * 验证码生成
     */
    public function vcodeAction(){
        Yaf_Dispatcher::getInstance()->autoRender(false)->disableView();
        \Common\Util\VerifyCode::createCode();
    }
} 
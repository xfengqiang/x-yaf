<?php
/**
 * @name IndexController
 * @author frank.xu
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class AccountController extends Abstract_Controller_Base {
    public function loginAction(){
    }
    public function logoutAction(){
        Yaf_Session::getInstance()->del('username');
        $this->redirect('/account/login');
    }
    public function vcodeAction(){
        Yaf_Dispatcher::getInstance()->autoRender(false)->disableView();
        Comm_Util_VerifyCode::createCode();
    }
}

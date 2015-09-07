<?php
/**
 * @name IndexController
 * @author frank.xu
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class AccountController extends \Http\Controller\Web {
    public function loginAction(){
    }
    public function registerAction(){
    }
    public function detailAction(){
        $this->disableView();
        $id = $this->requiredParams(['id']);
        echo "hello {$id['id']}";
    }
    public function logoutAction(){
        Yaf_Session::getInstance()->del('username');
        $this->redirect('/account/login');
    }
   
}

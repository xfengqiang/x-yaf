<?php
/**
 * 
 * @package aj
 * @Autor: fengqiang <fengqiang1@staff.sina.com.cn>
 * @Date: 2014-11-02 15:34
 */
class Aj_AccountController extends  Abstract_Controller_Aj{
    public function loginAction(){
        $name = $this->getPost('str', true, 'username');
        $password = $this->getPost('str', true, 'password');
        $vcode = $this->getPost('str', true, 'vcode');
        
        if(Comm_App::hasLogin()){
            $this->redirect('/');
        }
        
        if(!Comm_Util_VerifyCode::isValid($vcode)){
            $this->error(0, '验证码不正确');
        }
        
        if($name == 'frank' && $password=='z'){
            $session = Yaf_Session::getInstance();
            $session->offsetSet('username', 'frank');    
            $this->redirect('/');
        }else{
            $this->error('用户名或密码错误');
        }
    }
    
    public function logoutAction(){
        Yaf_Session::getInstance()->del('username');
        $this->redirect('/account/login');
    }
} 
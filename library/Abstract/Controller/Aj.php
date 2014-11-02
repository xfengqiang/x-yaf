<?php
/**
 * 
 * @package Abstract/Controller
 * @Autor: fengqiang <fengqiang1@staff.sina.com.cn>
 * @Date: 2014-11-02 13:30
 */
class Abstract_Controller_Aj extends Abstract_Controller_Base{
    public $need_login = true;
    
    public function init(){
        parent::init();
        Yaf_Registry::set('request_type', 'aj');
        //禁止自动渲染模板
        Yaf_Dispatcher::getInstance()->autoRender(false)->disableView();
    }

    /**
     * 输出结果
     * @param int $code
     * @param string $msg
     * @param mixed $data
     */
    public function result($code, $msg='', $data = null) {
        Comm_Response::contentType(Comm_Response::TYPE_JSON);
        $this->getResponse()->setBody(Comm_Response::json($code, $msg, $data));
    }
    
    public function error($code, $msg=''){
        throw new Yaf_Exception($msg, $code);
    }
} 
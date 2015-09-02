<?php
/**
 * 
 * @Autor: frank
 * @Date: 2015-05-23 15:08
 */

namespace Http\Controller;

use Common\Http\Response;

class Api extends Base{
    public function init(){
        parent::init();
        \Yaf_Dispatcher::getInstance()->autoRender(false)->disableView();
    }

    public function returnOk($data = array(), $msg='', $code=0) {
        $ret = array('code'=>$code, 'data'=>$data, 'msg'=>$msg);
        $this->getResponse()->setBody(Response::json($ret));
    }

    public function returnError($code, $msg=''){
        throw new \Yaf_Exception($msg, $code);
    }
} 